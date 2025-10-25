-- Demo Data for Testing PPPP Application
-- Run this after installing schema.sql

USE pppp_db;

-- Add more test users
INSERT INTO users (username, email, password, full_name, role, status) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'user', 'active'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 'manager', 'active'),
('bob_wilson', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob Wilson', 'user', 'active'),
('alice_brown', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice Brown', 'user', 'active');

-- Note: Password for all users is 'admin123'

-- Add sample tasks for admin user (ID 1)
INSERT INTO tasks (user_id, title, description, status, priority, due_date) VALUES
(1, 'Complete project proposal', 'Write and submit the Q4 project proposal to management', 'in_progress', 'high', DATE_ADD(CURDATE(), INTERVAL 3 DAY)),
(1, 'Review code changes', 'Review pull requests from the development team', 'pending', 'medium', DATE_ADD(CURDATE(), INTERVAL 1 DAY)),
(1, 'Update documentation', 'Update API documentation for version 2.0', 'pending', 'low', DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
(1, 'Team meeting preparation', 'Prepare slides for weekly team sync', 'completed', 'medium', CURDATE()),
(1, 'Fix critical bug', 'Address the login issue reported by users', 'in_progress', 'urgent', CURDATE());

-- Add sample tasks for other users
INSERT INTO tasks (user_id, title, description, status, priority, due_date) VALUES
(2, 'Client presentation', 'Prepare presentation for new client', 'pending', 'high', DATE_ADD(CURDATE(), INTERVAL 2 DAY)),
(3, 'Database optimization', 'Optimize slow-running queries', 'in_progress', 'medium', DATE_ADD(CURDATE(), INTERVAL 5 DAY)),
(4, 'User testing', 'Conduct user testing session', 'pending', 'low', DATE_ADD(CURDATE(), INTERVAL 10 DAY));

-- Add sample calendar events
INSERT INTO calendar_events (user_id, title, description, event_date, event_time, location) VALUES
(1, 'Team Standup', 'Daily team sync meeting', CURDATE(), '09:00:00', 'Conference Room A'),
(1, 'Client Demo', 'Product demonstration for ABC Corp', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', 'Virtual - Zoom'),
(1, 'Sprint Planning', 'Planning session for next sprint', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '10:00:00', 'Main Office'),
(1, 'Code Review Session', 'Weekly code review with team', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '15:00:00', 'Dev Room'),
(2, 'Marketing Meeting', 'Discuss Q4 marketing strategy', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', 'Marketing Dept'),
(3, 'Training Workshop', 'New tools and technologies workshop', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '13:00:00', 'Training Center');

-- Add sample messages
INSERT INTO messages (sender_id, receiver_id, message, is_read) VALUES
(2, 1, 'Hi! Can we schedule a meeting to discuss the project?', 0),
(1, 2, 'Sure! How about tomorrow at 2 PM?', 1),
(2, 1, 'That works perfectly. See you then!', 0),
(3, 1, 'The database optimization is complete. Ready for review.', 0),
(4, 1, 'I have completed the user testing. Results attached.', 0),
(1, 3, 'Great work on the optimization! I will review it today.', 1);

-- Add sample dashboard widgets for new users
INSERT INTO dashboard_widgets (user_id, widget_type, widget_position, is_visible) VALUES
(2, 'tasks', 0, 1),
(2, 'calendar', 1, 1),
(2, 'weather', 2, 1),
(2, 'inbox', 3, 1),
(3, 'tasks', 0, 1),
(3, 'calendar', 1, 1),
(3, 'weather', 2, 0),
(3, 'inbox', 3, 1),
(4, 'tasks', 0, 1),
(4, 'calendar', 1, 1),
(4, 'weather', 2, 1),
(4, 'inbox', 3, 0);

-- Add sample activity logs
INSERT INTO activity_logs (user_id, action, entity_type, entity_id, ip_address, user_agent) VALUES
(1, 'login', NULL, NULL, '192.168.1.100', 'Mozilla/5.0'),
(1, 'create_task', 'task', 1, '192.168.1.100', 'Mozilla/5.0'),
(2, 'login', NULL, NULL, '192.168.1.101', 'Mozilla/5.0'),
(2, 'send_message', 'message', 1, '192.168.1.101', 'Mozilla/5.0'),
(3, 'login', NULL, NULL, '192.168.1.102', 'Mozilla/5.0'),
(4, 'login', NULL, NULL, '192.168.1.103', 'Mozilla/5.0');

-- Success message
SELECT 'Demo data inserted successfully!' as Status;
SELECT COUNT(*) as 'Total Users' FROM users;
SELECT COUNT(*) as 'Total Tasks' FROM tasks;
SELECT COUNT(*) as 'Total Events' FROM calendar_events;
SELECT COUNT(*) as 'Total Messages' FROM messages;
