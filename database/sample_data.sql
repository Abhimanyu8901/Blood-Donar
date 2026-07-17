USE blood_saathi;

INSERT INTO donors (full_name, phone, email, blood_group, city, availability, contact_preference, last_donation_date, notes, status, is_verified)
VALUES
('Rohan Sharma', '+91 98111 22334', 'rohan@example.com', 'O+', 'New Delhi', 'Available today', 'WhatsApp', '2025-12-14', 'Can travel within South Delhi.', 'available', 1),
('Ananya Mehta', '+91 98222 33445', 'ananya@example.com', 'B+', 'New Delhi', 'Available now', 'Phone call', '2025-11-09', 'Prefers morning coordination.', 'available', 1),
('Imran Khan', '+91 98333 44556', 'imran@example.com', 'A-', 'Noida', 'Available this week', 'SMS', '2025-10-20', 'Travel radius of 12 km.', 'available', 0);

INSERT INTO donor_notification_preferences (donor_id, email_enabled, sms_enabled, whatsapp_enabled)
VALUES
(1, 1, 1, 1),
(2, 1, 0, 1),
(3, 1, 1, 0);

INSERT INTO users (donor_id, full_name, email, password_hash, role)
VALUES
(1, 'Rohan Sharma', 'rohan@example.com', '$2y$12$g22.4s.SrSXpTBLrWiPMMeMnfq4/4/EbwwTOpiV6LSOtRE0OFH1s2', 'donor'),
(NULL, 'Coordinator Demo', 'coordinator@bloodsaathi.org', '$2y$12$g22.4s.SrSXpTBLrWiPMMeMnfq4/4/EbwwTOpiV6LSOtRE0OFH1s2', 'coordinator'),
(NULL, 'Tester Demo', 'tester@bloodsaathi.org', '$2y$12$g22.4s.SrSXpTBLrWiPMMeMnfq4/4/EbwwTOpiV6LSOtRE0OFH1s2', 'tester');

INSERT INTO blood_requests (patient_name, blood_group, required_units, urgency, hospital, city, notes, status)
VALUES
('Priya Verma', 'O+', 2, 'Critical - within hours', 'City Care Hospital', 'New Delhi', 'ICU case, family reachable on-site.', 'open'),
('Aarav Singh', 'B+', 1, 'Today', 'Metro Heart Centre', 'Noida', 'Scheduled surgery this afternoon.', 'open');
