saya membutuhkan restfull api dengan susunan tabel sebagai berikut
-- 1. users
-- 2. wallets  
-- 3. wallet_members
-- 4. categories
-- 5. transactions
-- 6. recurring_schedules
-- 7. wallet_invitations
-- 8. web_access_tokens

tadinya saya menggunakan laravel scantum untuk auth api nya, maka tolong disesuaikan karena susunan tabel users akan saya ubah. berikut ini susunan berbagai tabel yang saya butuhkan 

CREATE TABLE users (
    id VARCHAR(36) PRIMARY KEY DEFAULT UUID(),
    email VARCHAR(255) UNIQUE NOT NULL,
    display_name VARCHAR(100),
    password_hash VARCHAR(255),
    is_email_verified BOOLEAN DEFAULT FALSE,
    subscription_status ENUM('Free', 'Premium') DEFAULT 'Free',
    active_wallet_id VARCHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (active_wallet_id) REFERENCES wallets(id) ON DELETE SET NULL
);


CREATE TABLE wallets (
    id VARCHAR(36) PRIMARY KEY DEFAULT UUID(),
    name VARCHAR(100) NOT NULL,
    current_balance DECIMAL(15,2) DEFAULT 0.00,
    privacy ENUM('Private', 'Shared') DEFAULT 'Private',
    admin_id VARCHAR(36) NOT NULL,
    member_count INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE wallet_members (
    id VARCHAR(36) PRIMARY KEY DEFAULT UUID(),
    wallet_id VARCHAR(36) NOT NULL,
    user_id VARCHAR(36) NOT NULL,
    role ENUM('Admin', 'Editor', 'Viewer') NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wallet_user (wallet_id, user_id),
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE categories (
    id VARCHAR(36) PRIMARY KEY DEFAULT UUID(),
    name VARCHAR(100) NOT NULL,
    type ENUM('Income', 'Expense') NOT NULL,
    icon VARCHAR(50) NOT NULL, 
    color VARCHAR(7) DEFAULT '#3B82F6',
    user_id VARCHAR(36), -- NULL untuk kategori default sistem
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE transactions (
    id VARCHAR(36) PRIMARY KEY DEFAULT UUID(),
    wallet_id VARCHAR(36) NOT NULL,
    type ENUM('Income', 'Expense') NOT NULL,
    category_id VARCHAR(36) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    description TEXT,
    date TIMESTAMP NOT NULL,
    proof_url VARCHAR(500),
    recorded_by VARCHAR(36) NOT NULL,
    is_recurring BOOLEAN DEFAULT FALSE,
    recurring_schedule_id VARCHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recurring_schedule_id) REFERENCES recurring_schedules(id) ON DELETE SET NULL
);


CREATE TABLE recurring_schedules (
    id VARCHAR(36) PRIMARY KEY DEFAULT UUID(),
    wallet_id VARCHAR(36) NOT NULL,
    type ENUM('Income', 'Expense') NOT NULL,
    category_id VARCHAR(36) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    description TEXT,
    interval_type ENUM('Daily', 'Weekly', 'Monthly', 'Yearly') NOT NULL,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP,
    next_run_date TIMESTAMP NOT NULL,
    reminder_before_days INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);


CREATE TABLE wallet_invitations (
    id VARCHAR(36) PRIMARY KEY DEFAULT UUID(),
    wallet_id VARCHAR(36) NOT NULL,
    recipient_email VARCHAR(255) NOT NULL,
    assigned_role ENUM('Editor', 'Viewer') NOT NULL,
    invite_token VARCHAR(100) UNIQUE NOT NULL,
    invited_by VARCHAR(36) NOT NULL,
    status ENUM('Pending', 'Accepted', 'Expired') DEFAULT 'Pending',
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE web_access_tokens (
    id VARCHAR(36) PRIMARY KEY DEFAULT UUID(),
    wallet_id VARCHAR(36) NOT NULL,
    token VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255),
    created_by VARCHAR(36) NOT NULL,
    expires_at TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);



### Index yang disarankan 
-- Users
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_subscription ON users(subscription_status);

-- Wallets
CREATE INDEX idx_wallets_admin ON wallets(admin_id);
CREATE INDEX idx_wallets_privacy ON wallets(privacy);

-- Wallet Members
CREATE INDEX idx_wallet_members_user ON wallet_members(user_id);
CREATE INDEX idx_wallet_members_wallet ON wallet_members(wallet_id);
CREATE INDEX idx_wallet_members_role ON wallet_members(role);

-- Transactions
CREATE INDEX idx_transactions_wallet_date ON transactions(wallet_id, date);
CREATE INDEX idx_transactions_type ON transactions(type);
CREATE INDEX idx_transactions_category ON transactions(category_id);
CREATE INDEX idx_transactions_recurring ON transactions(is_recurring);

-- Recurring Schedules
CREATE INDEX idx_recurring_next_run ON recurring_schedules(next_run_date, is_active);
CREATE INDEX idx_recurring_wallet ON recurring_schedules(wallet_id);

-- Invitations
CREATE INDEX idx_invitations_token ON wallet_invitations(invite_token);
CREATE INDEX idx_invitations_email ON wallet_invitations(recipient_email);
CREATE INDEX idx_invitations_status ON wallet_invitations(status);


### Catatan untuk implememtasi
#### Triger untuk update saldo
DELIMITER //
CREATE TRIGGER after_transaction_insert 
AFTER INSERT ON transactions
FOR EACH ROW
BEGIN
    IF NEW.type = 'Income' THEN
        UPDATE wallets 
        SET current_balance = current_balance + NEW.amount 
        WHERE id = NEW.wallet_id;
    ELSE
        UPDATE wallets 
        SET current_balance = current_balance - NEW.amount 
        WHERE id = NEW.wallet_id;
    END IF;
END//
DELIMITER ;

#### Data awal untuk kategori
-- Kategori default untuk pemasukan
INSERT INTO categories (id, name, type, is_default) VALUES
(UUID(), 'Gaji', 'Income', TRUE),
(UUID(), 'Investasi', 'Income', TRUE),
(UUID(), 'Bonus', 'Income', TRUE);

-- Kategori default untuk pengeluaran
INSERT INTO categories (id, name, type, is_default) VALUES
(UUID(), 'Makanan', 'Expense', TRUE),
(UUID(), 'Transportasi', 'Expense', TRUE),
(UUID(), 'Hiburan', 'Expense', TRUE);