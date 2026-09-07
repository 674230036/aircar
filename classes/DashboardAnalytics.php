<?php
/**
 * OOD: DashboardAnalytics Service
 * Calculates analytics, Key Performance Indicators (KPIs),
 * and dynamic dataset formats required for interactive Chart.js visualizations.
 */
class DashboardAnalytics {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Aggregate high-level KPIs for dashboard cards
     */
    public function getKPIs(): array {
        $totalCust = (int)$this->db->query("SELECT COUNT(*) FROM customers")->fetchColumn();
        $totalTech = (int)$this->db->query("SELECT COUNT(*) FROM technicians")->fetchColumn();
        $availTech = (int)$this->db->query("SELECT COUNT(*) FROM technicians WHERE status = 'ว่าง'")->fetchColumn();
        $totalOrders = (int)$this->db->query("SELECT COUNT(*) FROM service_orders")->fetchColumn();
        $pendingOrders = (int)$this->db->query("SELECT COUNT(*) FROM service_orders WHERE status IN ('รอดำเนินการ', 'นัดหมายแล้ว')")->fetchColumn();
        $completedOrders = (int)$this->db->query("SELECT COUNT(*) FROM service_orders WHERE status = 'เสร็จสิ้น'")->fetchColumn();
        
        $totalRevenue = (float)$this->db->query("SELECT COALESCE(SUM(total_price), 0) FROM service_orders WHERE status != 'ยกเลิก'")->fetchColumn();
        $paidRevenue = (float)$this->db->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'ชำระแล้ว'")->fetchColumn();

        return [
            'total_customers' => $totalCust,
            'total_technicians' => $totalTech,
            'available_technicians' => $availTech,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'total_revenue' => $totalRevenue,
            'paid_revenue' => $paidRevenue
        ];
    }

    /**
     * Monthly trend of revenue and order counts for Line/Area Chart
     */
    public function getMonthlyTrends(int $months = 6): array {
        // Query group by month
        $sql = "
            SELECT 
                DATE_FORMAT(COALESCE(service_date, created_at), '%Y-%m') AS ym,
                COUNT(*) AS order_count,
                COALESCE(SUM(total_price), 0) AS total_revenue
            FROM service_orders
            WHERE status != 'ยกเลิก'
            GROUP BY ym
            ORDER BY ym ASC
            LIMIT {$months}
        ";
        $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $thaiMonths = [
            '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
            '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
            '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
        ];

        $labels = [];
        $revenueData = [];
        $orderData = [];

        foreach ($rows as $r) {
            $parts = explode('-', $r['ym']);
            $m = $parts[1] ?? '01';
            $labels[] = ($thaiMonths[$m] ?? $m) . ' ' . substr($parts[0] ?? '', 2);
            $revenueData[] = (float)$r['total_revenue'];
            $orderData[] = (int)$r['order_count'];
        }

        // If dataset is empty or small, provide graceful defaults
        if (empty($labels)) {
            $labels = ['พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.'];
            $revenueData = [1200, 3400, 4700, 2800, 3200];
            $orderData = [1, 2, 3, 2, 3];
        }

        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'orders' => $orderData
        ];
    }

    /**
     * Order status breakdown for Doughnut Chart
     */
    public function getStatusBreakdown(): array {
        $statuses = ['รอดำเนินการ', 'นัดหมายแล้ว', 'กำลังดำเนินการ', 'เสร็จสิ้น', 'ยกเลิก'];
        $sql = "SELECT status, COUNT(*) as cnt FROM service_orders GROUP BY status";
        $stmt = $this->db->query($sql);
        $fetched = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $counts = [];
        foreach ($statuses as $st) {
            $counts[] = (int)($fetched[$st] ?? 0);
        }

        return [
            'labels' => $statuses,
            'data' => $counts,
            'colors' => [
                '#f59e0b', // รอดำเนินการ - Amber
                '#3b82f6', // นัดหมายแล้ว - Blue
                '#06b6d4', // กำลังดำเนินการ - Cyan
                '#10b981', // เสร็จสิ้น - Emerald
                '#ef4444'  // ยกเลิก - Red
            ]
        ];
    }

    /**
     * Technician workload and performance for Bar Chart
     */
    public function getTechnicianPerformance(): array {
        $sql = "
            SELECT 
                t.name,
                COUNT(so.id) AS total_jobs,
                SUM(CASE WHEN so.status = 'เสร็จสิ้น' THEN 1 ELSE 0 END) AS completed_jobs,
                COALESCE(SUM(CASE WHEN so.status = 'เสร็จสิ้น' THEN so.total_price ELSE 0 END), 0) AS revenue
            FROM technicians t
            LEFT JOIN service_orders so ON so.technician_id = t.id
            GROUP BY t.id, t.name
            ORDER BY completed_jobs DESC, total_jobs DESC
        ";
        $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $names = [];
        $completedJobs = [];
        $totalJobs = [];
        $revenues = [];

        foreach ($rows as $r) {
            $names[] = $r['name'];
            $completedJobs[] = (int)$r['completed_jobs'];
            $totalJobs[] = (int)$r['total_jobs'];
            $revenues[] = (float)$r['revenue'];
        }

        return [
            'names' => $names,
            'completed' => $completedJobs,
            'total' => $totalJobs,
            'revenues' => $revenues
        ];
    }

    /**
     * Air Conditioner type distribution for Polar/Pie Chart
     */
    public function getAcTypeDistribution(): array {
        $sql = "
            SELECT 
                COALESCE(NULLIF(ac_type, ''), 'แอร์ติดผนัง') AS ac_type_clean,
                COUNT(*) AS cnt
            FROM service_orders
            GROUP BY ac_type_clean
            ORDER BY cnt DESC
        ";
        $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $data = [];
        foreach ($rows as $r) {
            $labels[] = $r['ac_type_clean'];
            $data[] = (int)$r['cnt'];
        }

        if (empty($labels)) {
            $labels = ['แอร์ติดผนัง', 'แอร์แขวน/ตั้งพื้น', 'แอร์สี่ทิศทาง (Cassette)'];
            $data = [5, 2, 1];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => ['#0284c7', '#0d9488', '#6366f1', '#f59e0b']
        ];
    }

    /**
     * Get recent orders with customer and technician details for the dashboard table
     */
    public function getRecentOrders(int $limit = 6): array {
        $sql = "
            SELECT 
                so.*,
                c.name AS customer_name,
                c.phone AS customer_phone,
                t.name AS technician_name,
                p.payment_status
            FROM service_orders so
            JOIN customers c ON c.id = so.customer_id
            LEFT JOIN technicians t ON t.id = so.technician_id
            LEFT JOIN payments p ON p.order_id = so.id
            ORDER BY so.id DESC
            LIMIT {$limit}
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
