<?php
require_once __DIR__ . '/Customer.php';
require_once __DIR__ . '/Technician.php';
require_once __DIR__ . '/AirConditioner.php';
require_once __DIR__ . '/ServiceOrder.php';
require_once __DIR__ . '/Payment.php';
require_once __DIR__ . '/ServiceReport.php';

/**
 * OOD: BookingManager Service
 * Orchestrates business processes between Customer, Technician, ServiceOrder,
 * Payment, and ServiceReport entities, with Ban Pong geolocation and job completion.
 */
class BookingManager {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Preset subdistricts within Ban Pong District, Ratchaburi
     */
    public static function getBanPongSubdistricts(): array {
        return [
            'ต.บ้านโป่ง' => ['lat' => 13.8164, 'lng' => 99.8774, 'landmark' => 'หอนาฬิกาบ้านโป่ง, ตลาดบ้านโป่ง, สถานีรถไฟบ้านโป่ง'],
            'ต.ท่าผา' => ['lat' => 13.8055, 'lng' => 99.8920, 'landmark' => 'โรงงานกระดาษ SCG, ถนนแสงชูโต, ชุมชนท่าผา'],
            'ต.เบิกไพร' => ['lat' => 13.8280, 'lng' => 99.8650, 'landmark' => 'สะพานค่ายหลวง, วัดเบิกไพร, ริมน้ำแม่กลอง'],
            'ต.ปากแรต' => ['lat' => 13.8010, 'lng' => 99.8550, 'landmark' => 'วัดปากแรต, ถนนเลี่ยงเมืองบ้านโป่ง'],
            'ต.หนองกบ' => ['lat' => 13.8450, 'lng' => 99.9100, 'landmark' => 'วัดหนองกบ, สี่แยกสระกระเทียม-หนองกบ'],
            'ต.กรับใหญ่' => ['lat' => 13.9100, 'lng' => 99.8300, 'landmark' => 'ตลาดห้วยกระบอก, วัดกรับใหญ่'],
            'ต.คุ้งพยอม' => ['lat' => 13.8320, 'lng' => 99.8950, 'landmark' => 'วัดคุ้งพยอม, โรงเรียนคุ้งพยอม'],
            'ต.หนองปลาหมอ' => ['lat' => 13.8650, 'lng' => 99.8900, 'landmark' => 'วัดหนองปลาหมอ, ถนนบ้านโป่ง-กำแพงแสน'],
            'ต.ดอนกระเบื้อง' => ['lat' => 13.7920, 'lng' => 99.9200, 'landmark' => 'วัดดอนกระเบื้อง, แยกดอนกระเบื้อง'],
            'ต.นครชุมน์' => ['lat' => 13.7850, 'lng' => 99.8750, 'landmark' => 'วัดนครชุมน์, โบราณสถานมอญ'],
            'ต.บ้านม่วง' => ['lat' => 13.7750, 'lng' => 99.8600, 'landmark' => 'วัดบ้านม่วง, ศูนย์วัฒนธรรมไทย-รามัญ'],
            'ต.เขาขลุง' => ['lat' => 13.7950, 'lng' => 99.7800, 'landmark' => 'สี่แยกเขาขลุง, โรงเรียนเขาขลุง'],
            'ต.ลาดบัวขาว' => ['lat' => 13.8550, 'lng' => 99.8400, 'landmark' => 'วัดลาดบัวขาว, อนามัยลาดบัวขาว']
        ];
    }

    public function getTechnicians(): array {
        $stmt = $this->db->query("SELECT * FROM technicians ORDER BY status ASC, rating DESC, id ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $technicians = [];
        foreach ($rows as $row) {
            $technicians[] = new Technician(
                (int)$row['id'],
                $row['name'],
                $row['phone'] ?? '',
                $row['skill'] ?? 'ล้างแอร์ทั่วไป',
                $row['status'] ?? 'ว่าง',
                (float)($row['rating'] ?? 4.9),
                $row['experience'] ?? 'ประสบการณ์ 5 ปี',
                $row['avatar'] ?? ''
            );
        }
        return $technicians;
    }

    public function getTechnicianById(int $id): ?Technician {
        $stmt = $this->db->prepare("SELECT * FROM technicians WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new Technician(
            (int)$row['id'],
            $row['name'],
            $row['phone'] ?? '',
            $row['skill'] ?? 'ล้างแอร์ทั่วไป',
            $row['status'] ?? 'ว่าง',
            (float)($row['rating'] ?? 4.9),
            $row['experience'] ?? 'ประสบการณ์ 5 ปี',
            $row['avatar'] ?? ''
        );
    }

    /**
     * Process a customer booking submission (OOA/OOD Transaction)
     */
    public function processBooking(array $postData): array {
        $name = trim($postData['customer_name'] ?? '');
        $phone = trim($postData['customer_phone'] ?? '');
        $email = trim($postData['customer_email'] ?? '');
        $subdistrict = trim($postData['subdistrict'] ?? 'ต.บ้านโป่ง');
        $address = trim($postData['customer_address'] ?? '');
        $technicianId = !empty($postData['technician_id']) ? (int)$postData['technician_id'] : null;
        $serviceDate = trim($postData['service_date'] ?? '');
        $serviceTime = trim($postData['service_time'] ?? '09:00');
        $acType = trim($postData['ac_type'] ?? 'แอร์ติดผนัง');
        $acBrand = trim($postData['ac_brand'] ?? 'ไม่ระบุ');
        $units = max(1, (int)($postData['ac_units'] ?? 1));
        $paymentMethod = trim($postData['payment_method'] ?? 'พร้อมเพย์ QR Code');
        $addons = $postData['addons'] ?? [];
        $note = trim($postData['note'] ?? '');

        // Ban Pong coordinates
        $subPresets = self::getBanPongSubdistricts();
        $defaultPreset = $subPresets[$subdistrict] ?? $subPresets['ต.บ้านโป่ง'];
        $lat = !empty($postData['latitude']) ? (float)$postData['latitude'] : $defaultPreset['lat'];
        $lng = !empty($postData['longitude']) ? (float)$postData['longitude'] : $defaultPreset['lng'];
        $mapUrl = "https://www.google.com/maps?q={$lat},{$lng}";

        // Combine full address with Ban Pong district
        $fullAddress = $address;
        if (!str_contains($fullAddress, 'บ้านโป่ง')) {
            $fullAddress .= " {$subdistrict} อ.บ้านโป่ง จ.ราชบุรี";
        }

        // Validation
        if (empty($name)) throw new InvalidArgumentException("กรุณากรอกชื่อ-นามสกุลลูกค้า");
        if (empty($phone)) throw new InvalidArgumentException("กรุณากรอกเบอร์โทรศัพท์ติดต่อ");
        if (empty($address)) throw new InvalidArgumentException("กรุณากรอกที่อยู่สำหรับให้บริการใน อ.บ้านโป่ง");
        if (empty($serviceDate)) throw new InvalidArgumentException("กรุณาเลือกวันที่ต้องการรับบริการ");

        $fullServiceDateTime = date('Y-m-d H:i:s', strtotime("{$serviceDate} {$serviceTime}"));

        $this->db->beginTransaction();
        try {
            // 1. Create or retrieve Customer (OOP Customer Entity)
            $custStmt = $this->db->prepare("SELECT * FROM customers WHERE phone = ? LIMIT 1");
            $custStmt->execute([$phone]);
            $custRow = $custStmt->fetch(PDO::FETCH_ASSOC);

            if ($custRow) {
                $customerId = (int)$custRow['id'];
                $upStmt = $this->db->prepare("UPDATE customers SET name = ?, email = ?, address = ?, subdistrict = ?, latitude = ?, longitude = ? WHERE id = ?");
                $upStmt->execute([$name, $email ?: $custRow['email'], $fullAddress, $subdistrict, $lat, $lng, $customerId]);
                $customer = new Customer($customerId, $name, $phone, $email ?: $custRow['email'], $fullAddress, $subdistrict, $lat, $lng);
            } else {
                $insCust = $this->db->prepare("INSERT INTO customers (name, phone, email, address, subdistrict, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insCust->execute([$name, $phone, $email ?: null, $fullAddress, $subdistrict, $lat, $lng]);
                $customerId = (int)$this->db->lastInsertId();
                $customer = new Customer($customerId, $name, $phone, $email, $fullAddress, $subdistrict, $lat, $lng);
            }

            // 2. Air conditioner entity & pricing calculation
            $basePricePerUnit = AirConditioner::getBasePriceForType($acType);
            $addonTotal = 0.0;
            $addonNotes = [];
            if (is_array($addons)) {
                if (in_array('ozone', $addons)) {
                    $addonTotal += (150 * $units);
                    $addonNotes[] = "อบโอโซนฆ่าเชื้อ ({$units} เครื่อง)";
                }
                if (in_array('gas_check', $addons)) {
                    $addonTotal += (200 * $units);
                    $addonNotes[] = "ตรวจเช็คน้ำยาแอร์ ({$units} เครื่อง)";
                }
                if (in_array('deep_clean', $addons)) {
                    $addonTotal += (300 * $units);
                    $addonNotes[] = "ล้างแบบ Deep Clean ถอดทุกชิ้น ({$units} เครื่อง)";
                }
            }

            $order = new ServiceOrder(null, $customerId, $technicianId, $fullServiceDateTime, 'รอดำเนินการ', 0.0, '', null, $acType, $units, $subdistrict, $lat, $lng, $mapUrl);
            $totalPrice = $order->calculatePrice($units, $basePricePerUnit, $addonTotal);

            // Compose note
            $finalNoteParts = [];
            $finalNoteParts[] = "พิกัด: {$subdistrict} อ.บ้านโป่ง จ.ราชบุรี";
            $finalNoteParts[] = "ประเภท: {$acType} ({$units} เครื่อง) ยี่ห้อ: {$acBrand}";
            if (!empty($addonNotes)) {
                $finalNoteParts[] = "บริการเสริม: " . implode(', ', $addonNotes);
            }
            if (!empty($note)) {
                $finalNoteParts[] = "จุดสังเกต/หมายเหตุ: {$note}";
            }
            $finalNote = implode("\n", $finalNoteParts);

            // Unique Booking Code
            $bookingCode = 'AC-BP-' . date('Ymd') . '-' . rand(1000, 9999);

            // 3. Insert Service Order with Ban Pong coordinates & Map URL
            $insOrder = $this->db->prepare("INSERT INTO service_orders (customer_id, technician_id, service_date, status, total_price, note, booking_code, ac_type, units, subdistrict, latitude, longitude, map_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $insOrder->execute([
                $customerId,
                $technicianId,
                $fullServiceDateTime,
                $technicianId ? 'นัดหมายแล้ว' : 'รอดำเนินการ',
                $totalPrice,
                $finalNote,
                $bookingCode,
                $acType,
                $units,
                $subdistrict,
                $lat,
                $lng,
                $mapUrl
            ]);
            $orderId = (int)$this->db->lastInsertId();

            // 4. Save Air Conditioner unit record
            $insAc = $this->db->prepare("INSERT INTO air_conditioners (customer_id, brand, model, type, capacity, location, last_cleaning) VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
            $insAc->execute([$customerId, $acBrand, 'Standard', $acType, '12000 BTU', "อ.บ้านโป่ง ({$subdistrict})"]);

            // 5. Create Payment record
            $insPay = $this->db->prepare("INSERT INTO payments (order_id, amount, payment_method, payment_status, payment_date) VALUES (?, ?, ?, ?, NULL)");
            $insPay->execute([$orderId, $totalPrice, $paymentMethod, 'รอตรวจสอบ']);

            // 6. Update technician status to 'ไม่ว่าง' if assigned
            if ($technicianId) {
                $this->db->prepare("UPDATE technicians SET status = 'ไม่ว่าง' WHERE id = ?")->execute([$technicianId]);
            }

            $this->db->commit();

            $technician = $technicianId ? $this->getTechnicianById($technicianId) : null;

            return [
                'success' => true,
                'order_id' => $orderId,
                'booking_code' => $bookingCode,
                'customer' => $customer,
                'technician' => $technician,
                'total_price' => $totalPrice,
                'service_date' => $fullServiceDateTime,
                'formatted_date' => (new ServiceOrder($orderId, $customerId, $technicianId, $fullServiceDateTime))->getFormattedDate(),
                'ac_type' => $acType,
                'units' => $units,
                'subdistrict' => $subdistrict,
                'map_url' => $mapUrl,
                'payment_method' => $paymentMethod
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * OOA/OOD: Complete Job Workflow ("ปิดงาน / จบงาน")
     * Updates order status to 'เสร็จสิ้น', records technical service report,
     * settles payment, and releases technician availability.
     */
    public function completeJob(int $orderId, array $reportData): array {
        $stmt = $this->db->prepare("SELECT * FROM service_orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            throw new Exception("ไม่พบรหัสงานบริการ #{$orderId}");
        }

        $technicianId = (int)($order['technician_id'] ?? ($reportData['technician_id'] ?? 1));
        $cleaningResult = trim($reportData['cleaning_result'] ?? 'ล้างทำความสะอาดแผงคอยล์เย็น โบลเวอร์ และถาดน้ำทิ้งสะอาดสมบูรณ์');
        $gasBefore = trim($reportData['gas_psi_before'] ?? '');
        $gasAfter = trim($reportData['gas_psi_after'] ?? '');
        $current = trim($reportData['electric_current'] ?? '');
        $problemFound = trim($reportData['problem_found'] ?? 'ไม่มีรอยรั่วซึม เครื่องทำงานปกติ');
        $recommendation = trim($reportData['recommendation'] ?? 'ควรล้างทำความสะอาดแผ่นกรองฝุ่นทุกเดือน และล้างใหญ่ทุก 6 เดือน');
        $paymentMethod = trim($reportData['payment_method'] ?? 'พร้อมเพย์ QR Code');

        $this->db->beginTransaction();
        try {
            // 1. Insert or update service report
            $chkReport = $this->db->prepare("SELECT id FROM service_reports WHERE order_id = ?");
            $chkReport->execute([$orderId]);
            $existingReportId = $chkReport->fetchColumn();

            if ($existingReportId) {
                $upReport = $this->db->prepare("
                    UPDATE service_reports 
                    SET technician_id = ?, cleaning_result = ?, problem_found = ?, recommendation = ?, 
                        gas_psi_before = ?, gas_psi_after = ?, electric_current = ?, completed_at = NOW()
                    WHERE id = ?
                ");
                $upReport->execute([$technicianId, $cleaningResult, $problemFound, $recommendation, $gasBefore, $gasAfter, $current, $existingReportId]);
            } else {
                $insReport = $this->db->prepare("
                    INSERT INTO service_reports 
                    (order_id, technician_id, cleaning_result, problem_found, recommendation, gas_psi_before, gas_psi_after, electric_current, completed_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $insReport->execute([$orderId, $technicianId, $cleaningResult, $problemFound, $recommendation, $gasBefore, $gasAfter, $current]);
            }

            // 2. Update service order status to 'เสร็จสิ้น'
            $this->db->prepare("UPDATE service_orders SET status = 'เสร็จสิ้น' WHERE id = ?")->execute([$orderId]);

            // 3. Mark payment as 'ชำระแล้ว'
            $this->db->prepare("
                UPDATE payments 
                SET payment_status = 'ชำระแล้ว', payment_method = ?, payment_date = NOW() 
                WHERE order_id = ?
            ")->execute([$paymentMethod, $orderId]);

            // 4. Free up technician to 'ว่าง'
            if ($technicianId) {
                $this->db->prepare("UPDATE technicians SET status = 'ว่าง' WHERE id = ?")->execute([$technicianId]);
            }

            $this->db->commit();
            return ['success' => true, 'order_id' => $orderId, 'message' => 'บันทึกปิดงานและรายงานผลการล้างสำเร็จเรียบร้อย'];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findBookingForCustomer(string $keyword): array {
        $keyword = trim($keyword);
        if (empty($keyword)) return [];

        $stmt = $this->db->prepare("
            SELECT so.*, c.name AS customer_name, c.phone AS customer_phone, c.address AS customer_address,
                   t.name AS technician_name, t.phone AS technician_phone, t.skill AS technician_skill,
                   p.payment_status, p.payment_method,
                   sr.cleaning_result, sr.problem_found, sr.recommendation, sr.gas_psi_before, sr.gas_psi_after, sr.electric_current, sr.completed_at
            FROM service_orders so
            JOIN customers c ON c.id = so.customer_id
            LEFT JOIN technicians t ON t.id = so.technician_id
            LEFT JOIN payments p ON p.order_id = so.id
            LEFT JOIN service_reports sr ON sr.order_id = so.id
            WHERE so.booking_code = ? OR c.phone = ? OR so.id = ?
            ORDER BY so.id DESC
        ");
        $numericId = is_numeric($keyword) ? (int)$keyword : 0;
        $stmt->execute([$keyword, $keyword, $numericId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
