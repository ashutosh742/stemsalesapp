<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT Business Analytics Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Date Range Picker Styles */
        .date-range-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            max-width: 80%;
            margin: 0 auto 20px;
        }
        
        .date-input-group {
            flex: 1;
        }
        
        .date-input-group label {
            display: block;
            margin-bottom: 5px;
            color: #8e8ea0;
            font-size: 14px;
        }
        
        .date-input {
            width: 100%;
            padding: 10px 15px;
            background-color: #343541;
            border: 1px solid #565869;
            border-radius: 6px;
            color: white;
            font-size: 14px;
        }
        
        .date-input:focus {
            outline: none;
            border-color: #10a37f;
        }
        
        .quick-date-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            max-width: 80%;
            margin: 0 auto 20px;
            flex-wrap: wrap;
        }
        
        .quick-date-btn {
            background-color: #2d2d3a;
            color: #c5c5d2;
            border: 1px solid #4d4d4f;
            border-radius: 6px;
            padding: 8px 15px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .quick-date-btn:hover {
            background-color: #40414f;
            color: white;
        }
        
        .quick-date-btn.active {
            background-color: #10a37f;
            color: white;
            border-color: #10a37f;
        }
        
        .analysis-header {
            max-width: 80%;
            margin: 0 auto 20px;
            padding: 15px;
            background-color: #171717;
            border-radius: 10px;
            border: 1px solid #2a2a2a;
        }
        
        .analysis-header h3 {
            margin-bottom: 10px;
            color: #ececf1;
        }
        
        .analysis-header p {
            color: #8e8ea0;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .date-info {
            display: inline-block;
            background-color: #2d2d3a;
            color: #10a37f;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 13px;
            margin-top: 10px;
        }
        
        /* Explore Grid Styles */
        .explore-grid {
            display: flex;
            flex-direction: column;
            gap: 30px;
            max-width: 1200px;
            margin: 20px auto;
            width: 100%;
        }
        
        .explore-card {
            background-color: #171717;
            border: 1px solid #2a2a2a;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            min-height: 140px;
            justify-content: center;
        }
        
        .explore-card:hover {
            background-color: #2d2d3a;
            transform: translateY(-2px);
            border-color: #10a37f;
        }
        
        .explore-card i {
            font-size: 28px;
            margin-bottom: 12px;
            color: #10a37f;
        }
        
        .explore-card h4 {
            font-size: 15px;
            margin-bottom: 8px;
            color: #ececf1;
            font-weight: 500;
        }
        
        .explore-card p {
            font-size: 12px;
            color: #8e8ea0;
            line-height: 1.4;
        }
        
        .explore-category {
            margin-bottom: 30px;
            width: 100%;
        }
        
        .explore-category h3 {
            color: #8e8ea0;
            font-size: 16px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #2a2a2a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        
        /* Rest of the CSS remains the same */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        body {
            display: flex;
            height: 100vh;
            background-color: #0a0a0a;
            color: #fff;
            overflow: hidden;
        }
        
        .sidebar {
            width: 280px;
            background-color: #171717;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #2a2a2a;
            padding: 15px 12px;
            overflow-y: auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
        
        .logo i {
            font-size: 24px;
            color: #10a37f;
            margin-right: 10px;
        }
        
        .logo h1 {
            font-size: 20px;
            font-weight: 600;
        }
        
        .new-chat-btn {
            background-color: #343541;
            color: white;
            border: 1px solid #4d4d4f;
            border-radius: 6px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin-bottom: 20px;
            transition: all 0.2s;
        }
        
        .new-chat-btn:hover {
            background-color: #40414f;
        }
        
        .new-chat-btn i {
            margin-right: 8px;
        }
        
        .nav-section {
            margin-bottom: 25px;
        }
        
        .nav-title {
            font-size: 12px;
            color: #8e8ea0;
            text-transform: uppercase;
            padding: 5px 15px;
            margin-bottom: 8px;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 2px;
            color: #ececf1;
            text-decoration: none;
        }
        
        .nav-item:hover, .nav-item.active {
            background-color: #2d2d3a;
        }
        
        .nav-item i {
            margin-right: 12px;
            font-size: 16px;
            color: #8e8ea0;
            width: 20px;
            text-align: center;
        }
        
        .nav-item.active i {
            color: #10a37f;
        }
        
        .user-section {
            padding: 15px;
            border-top: 1px solid #2a2a2a;
            margin-top: auto;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            background-color: #10a37f;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .upgrade-btn {
            background-color: #10a37f;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: background-color 0.2s;
        }
        
        .upgrade-btn:hover {
            background-color: #0d8c6d;
        }
        
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: #0a0a0a;
        }
        
        .header {
            padding: 20px;
            border-bottom: 1px solid #2a2a2a;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .header h2 {
            font-size: 24px;
            font-weight: 600;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
        }
        
        .header-btn {
            background: none;
            border: 1px solid #4d4d4f;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 10px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .header-btn:hover {
            background-color: #2d2d3a;
        }
        
        .chat-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 81px);
        }
        
        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .message {
            display: flex;
            gap: 15px;
            max-width: 80%;
            margin: 0 auto;
            width: 100%;
        }
        
        .message-avatar {
            width: 36px;
            height: 36px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .user-msg .message-avatar {
            background-color: #10a37f;
        }
        
        .assistant-msg .message-avatar {
            background-color: #5436da;
        }
        
        .message-content {
            flex-grow: 1;
            padding-top: 5px;
        }
        
        .message-role {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .message-text {
            line-height: 1.5;
            color: #d1d5db;
        }
        
        .assistant-msg .message-text {
            white-space: pre-wrap;
        }
        
        .empty-chat {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
        }
        
        .chat-icon {
            font-size: 48px;
            color: #10a37f;
            margin-bottom: 20px;
        }
        
        .chat-title {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .chat-subtitle {
            font-size: 16px;
            color: #8e8ea0;
            margin-bottom: 30px;
            max-width: 600px;
        }
        
        .fixed-chat-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            max-width: 900px;
            margin-top: 20px;
        }
        
        .fixed-chat-card {
            background-color: #171717;
            border: 1px solid #2a2a2a;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 120px;
        }
        
        .fixed-chat-card:hover {
            background-color: #2d2d3a;
            transform: translateY(-2px);
            border-color: #10a37f;
        }
        
        .fixed-chat-card i {
            font-size: 24px;
            color: #10a37f;
            margin-bottom: 10px;
        }
        
        .fixed-chat-card h4 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #ececf1;
        }
        
        .fixed-chat-card p {
            font-size: 12px;
            color: #8e8ea0;
        }
        
        .input-container {
            padding: 20px;
            border-top: 1px solid #2a2a2a;
        }
        
        .input-wrapper {
            max-width: 80%;
            margin: 0 auto;
            position: relative;
        }
        
        .chat-input {
            width: 100%;
            background-color: #343541;
            border: 1px solid #565869;
            border-radius: 12px;
            padding: 15px 50px 15px 20px;
            color: white;
            font-size: 16px;
            resize: none;
            min-height: 56px;
            max-height: 200px;
        }
        
        .chat-input:focus {
            outline: none;
            border-color: #10a37f;
        }
        
        .send-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #10a37f;
            color: white;
            border: none;
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .send-btn:hover {
            background-color: #0d8c6d;
        }
        
        .send-btn:disabled {
            background-color: #565869;
            cursor: not-allowed;
        }
        
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 10px 0;
            color: #8e8ea0;
            font-size: 14px;
        }
        
        .typing-dots {
            display: flex;
            gap: 3px;
        }
        
        .typing-dots span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #8e8ea0;
            animation: typing 1.4s infinite ease-in-out;
        }
        
        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
        
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
        
        .footer-text {
            font-size: 12px;
            color: #8e8ea0;
            text-align: center;
            margin-top: 10px;
        }
        
        .projects-container, .gpts-container, .explore-container {
            display: none;
            padding: 20px;
            overflow-y: auto;
        }
        
        .projects-grid, .gpts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .project-card, .gpt-card {
            background-color: #171717;
            border: 1px solid #2a2a2a;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .project-card:hover, .gpt-card:hover {
            background-color: #2d2d3a;
            transform: translateY(-2px);
        }
        
        .project-card h3, .gpt-card h3 {
            margin-bottom: 10px;
            color: #ececf1;
        }
        
        .project-card p, .gpt-card p {
            color: #8e8ea0;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .project-tags, .gpt-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .tag {
            background-color: #2d2d3a;
            color: #8e8ea0;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        
        .data-table {
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #171717;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .data-table th {
            background-color: #2d2d3a;
            padding: 12px 15px;
            text-align: left;
            color: #ececf1;
            font-weight: 600;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-top: 1px solid #2a2a2a;
            color: #c5c5d2;
        }
        
        .data-table tr:hover {
            background-color: #2d2d3a;
        }
        
        .chart-container {
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
            background-color: #171717;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #2a2a2a;
        }
        
        @media (max-width: 1024px) {
            .sidebar {
                width: 240px;
            }
            
            .fixed-chat-options {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .projects-grid, .gpts-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .date-range-selector {
                flex-direction: column;
            }
            
            .category-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            
            .header h2 {
                font-size: 20px;
            }
            
            .fixed-chat-options {
                grid-template-columns: 1fr;
            }
            
            .category-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 480px) {
            .category-grid {
                grid-template-columns: 1fr;
            }
        }
        
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #565869;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #6e6e80;
        }
        
        .loading-spinner {
            border: 3px solid #2d2d3a;
            border-top: 3px solid #10a37f;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-robot"></i>
            <h1 style='text-align:center;'>SALES CRM <br> <small>Business Analytics AI</small> </h1>
        </div>
        
        <button class="new-chat-btn" id="newChatBtn">
            <i class="fas fa-plus"></i> New Analysis
        </button>
        
        <div class="nav-section">
            <div class="nav-title">Analytics Menu</div>
            <a href="#" class="nav-item" data-view="explore">
                <i class="fas fa-compass"></i> Explore Data
            </a>
            <a href="#" class="nav-item" data-view="logo-creator">
                <i class="fas fa-chart-bar"></i> Dashboard
            </a>
           
        </div>
        
        <!-- Removed Recent Analysis Section -->
        
        <div class="user-section">
            <div class="user-profile">
                <div>
                    <div style="font-weight: 500;text-align:center;">
                        <img style='width:200px;' src="https://stemlearning.in/wp-content/uploads/2020/07/stem-new-logo-2-1.png" alt="">
                       <br> <small style='color:green;'><?= $users[0]['name'] ?></small>
                       <br> <small style='color:blue;'><?= $this->Menu_model->get_user_type_data($users[0]['type_id'])[0]->name; ?></small>
                       <br> <small style='color:red;'><?= date("H:i:s"); ?></small>
                    </div>
                </div>
            </div>
            <!-- <button class="upgrade-btn" id="upgradeBtn">
                <i class="fas fa-chart-line"></i> Advanced Analytics
            </button> -->
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h2 id="headerTitle">Business Analytics Dashboard</h2>
            <div class="header-actions">
                <!-- <button class="header-btn" id="themeToggle">
                    <i class="fas fa-sun"></i> Light mode
                </button>
                <button class="header-btn" id="settingsBtn">
                    <i class="fas fa-cog"></i> Settings
                </button> -->
                <button class="header-btn" id="userProfileBtn" style='background-color:green;'>
                    <i class="fas fa-cog"></i> <?= $users[0]['name'] ?>
                </button>
            </div>
        </div>
        
        <!-- Chat View -->
        <div class="chat-container" id="chatView">
            <div class="chat-messages" id="chatMessages">
                <!-- Messages will be loaded here -->
            </div>
            <div class="input-container">
                <div class="input-wrapper">
                    <!-- Date Range Selector -->
                    <div class="date-range-selector">
                        <div class="date-input-group">
                            <label for="startDate"><i class="fas fa-calendar-alt"></i> Start Date</label>
                            <input type="date" id="startDate" class="date-input" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                        </div>
                        <div class="date-input-group">
                            <label for="endDate"><i class="fas fa-calendar-alt"></i> End Date</label>
                            <input type="date" id="endDate" class="date-input" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <!-- Quick Date Buttons -->
                    <div class="quick-date-buttons" id="quickDateButtons">
                        <button class="quick-date-btn" data-days="1">Today</button>
                        <button class="quick-date-btn" data-days="7">Last 7 Days</button>
                        <button class="quick-date-btn active" data-days="30">Last 30 Days</button>
                        <button class="quick-date-btn" data-days="90">Last 90 Days</button>
                        <button class="quick-date-btn" data-days="this_month">This Month</button>
                        <button class="quick-date-btn" data-days="last_month">Last Month</button>
                        <button class="quick-date-btn" data-days="this_quarter">This Quarter</button>
                        <button class="quick-date-btn" data-days="this_year">This Year</button>
                    </div>
                    
                    <textarea class="chat-input" id="chatInput" placeholder="Ask about business analytics, performance metrics, or data insights... You can also specify date range in your message." rows="1"></textarea>
                    <button class="send-btn" id="sendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="footer-text">
                    <i class="fas fa-exclamation-circle"></i> AI-generated insights. Always verify with actual data.
                </div>
            </div>
        </div>
        
        <!-- Projects View -->
        <div class="projects-container" id="projectsView">
            <div class="empty-chat">
                <div class="chat-icon">
                    <i class="fas fa-cube"></i>
                </div>
                <h1 class="chat-title">Analytics Projects</h1>
                <p class="chat-subtitle">Manage your business intelligence projects and data analysis initiatives.</p>
                <div class="projects-grid" id="projectsGrid">
                    <!-- Projects will be loaded here -->
                </div>
            </div>
        </div>
        
        <!-- GPTs View -->
        <div class="gpts-container" id="gptsView">
            <div class="empty-chat">
                <div class="chat-icon">
                    <i class="fas fa-magic"></i>
                </div>
                <h1 class="chat-title">AI Analysis Models</h1>
                <p class="chat-subtitle">Specialized AI models for different business analytics tasks.</p>
                <div class="gpts-grid" id="gptsGrid">
                    <!-- GPTs will be loaded here -->
                </div>
            </div>
        </div>
        
        <!-- Explore View -->
        <div class="explore-container" id="exploreView">
            <div class="empty-chat">
                <div class="chat-icon">
                    <i class="fas fa-compass"></i>
                </div>
                <h1 class="chat-title">Explore Business Data</h1>
                <p class="chat-subtitle">Discover insights from your business data across different domains.</p>
                <div class="explore-grid" id="exploreGrid">
                    <!-- Cards will be dynamically loaded here -->
                </div>
            </div>
        </div>
        
        <!-- Library View -->
        <div class="explore-container" id="libraryView">
            <div class="empty-chat">
                <div class="chat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h1 class="chat-title">Reports Library</h1>
                <p class="chat-subtitle">Access saved reports and historical analysis documents.</p>
            </div>
        </div>
        
        <!-- Dashboard View -->
        <div class="explore-container" id="logoCreatorView">
            <div class="empty-chat">
                <div class="chat-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h1 class="chat-title">Analytics Dashboard</h1>
                <p class="chat-subtitle">Interactive dashboard with key business metrics and visualizations.</p>
                <div class="chart-container">
                    <h3 style="margin-bottom: 15px; color: #ececf1;">Performance Overview</h3>
                    <div style="height: 300px; background: linear-gradient(45deg, #1a1a2e, #16213e); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #8e8ea0;">
                        <i class="fas fa-chart-pie" style="font-size: 48px; margin-right: 15px;"></i>
                        <span>Interactive charts would appear here</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Business Analytics AI Controller
        const BusinessAnalyticsController = {
            // Configuration
            config: {
                // CI3 Controller Endpoints
                apiBaseUrl: '<?php echo base_url(); ?>', // Your CodeIgniter base URL
                endpoints: {
                    chat: 'DeepChat/process', // CI3 controller/method for chat
                    analysis: 'analysis/generate', // CI3 controller/method for analysis
                    reports: 'reports/get', // CI3 controller/method for reports
                    data: 'data/query' // CI3 controller/method for data queries
                },
                // Fixed Analysis Types
                analysisTypes: {
                    taskAnalysis: 'Task Analysis',
                    taskAnalysis1: 'Task Analysis By Company',
                    taskPlanning: 'Task Planning',
                    taskExecution: 'Task Execution',
                    statusChange: 'Status Change',
                    funnelAnalysis: 'Funnel Analysis',
                    actionAnalysis: 'Action Analysis',
                    dayInDayOut: 'Day in Day Out',
                    efficiencyProductivity: 'Efficiency & Productivity',
                    dailyPerformance: 'Daily Performance',
                    bdPerformance: 'BD Performance',
                    cmPerformance: 'CM Performance'
                },
                storageKey: 'business_analytics_conversations'
            },
            
            // State
            state: {
                currentChatId: null,
                conversations: [],
                isDarkMode: true,
                currentView: 'chat',
                isTyping: false,
                currentAnalysisType: null,
                selectedDateRange: {
                    startDate: '',
                    endDate: ''
                },
                activeQuickDateBtn: '30' // Track active quick date button
            },
            
            // Initialize the application
            init() {
                this.loadStateFromStorage();
                this.initializeDateRange();
                this.bindEvents();
                this.renderExploreCards();
                this.switchView('chat');
                
                // Show welcome screen for fresh chat
                this.showWelcomeScreen();
                
                console.log('Business Analytics Dashboard initialized');
            },
            
            // Initialize date range
            initializeDateRange() {
                const today = new Date();
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(today.getDate() - 30);
                
                this.state.selectedDateRange = {
                    startDate: thirtyDaysAgo.toISOString().split('T')[0],
                    endDate: today.toISOString().split('T')[0]
                };
                
                // Set default values in date inputs
                document.getElementById('startDate').value = this.state.selectedDateRange.startDate;
                document.getElementById('endDate').value = this.state.selectedDateRange.endDate;
                
                // Set active button for Last 30 Days
                this.updateQuickDateButtons();
            },
            
            // Load state from localStorage
            loadStateFromStorage() {
                // Conversations are still saved but not displayed
                localStorage.removeItem('business_analytics_conversations');
                const savedConversations = localStorage.getItem(this.config.storageKey);
                
                if (savedConversations) {
                    this.state.conversations = JSON.parse(savedConversations);
                }
            },
            
            // Save conversations to localStorage
            saveConversations() {
                localStorage.setItem(this.config.storageKey, JSON.stringify(this.state.conversations));
            },
            
            // Render explore business data cards
            renderExploreCards() {
                const container = document.getElementById('exploreGrid');
                if (!container) return;
                
                const exploreCategories = [
                    {
                        title: "Team & Task Management",
                        cards: [
                            { icon: "👥", title: "Team Detail", desc: "View comprehensive team information" },
                            { icon: "🗂️", title: "Task Analysis", desc: "Complete / Pending task performance overview" },
                            { icon: "📝", title: "Task Details By Company", desc: "Individual task breakdown and status" },
                            { icon: "📅", title: "Meeting Details", desc: "Meeting schedules and outcomes" },
                            { icon: "📅", title: "Meeting Details With Company Name", desc: "Meeting schedules and outcomes With Company Name" },
                            { icon: "📨", title: "Proposal Details", desc: "Proposal tracking and status" },
                            { icon: "📊", title: "Meeting v/s Proposal Summary", desc: "Conversion analysis" },
                            { icon: "🔮", title: "Future Task Details", desc: "Upcoming task planning" },
                            { icon: "🔄", title: "Task Conversion Summary", desc: "Task completion analytics" }
                        ]
                    },
                    {
                        title: "Communication & Follow-up",
                        cards: [
                            { icon: "📝", title: "Pending MoM Write After RP Meetings", desc: "Minutes of meeting pending" },
                            { icon: "🗂️", title: "Line Manager Calling on RP Leads", desc: "Manager follow-up tracking" },
                            { icon: "🔁", title: "Funnels Report", desc: "Sales funnel performance" },
                            { icon: "📊", title: "Funnel Details", desc: "Detailed funnel stage analysis" },
                            { icon: "⚠️", title: "Companies without primary contact", desc: "Missing contact alerts" },
                            { icon: "🏷️", title: "Closing Timeline Target", desc: "Target vs actual closure" }
                        ]
                    },
                    {
                        title: "Status & Activity Monitoring",
                        cards: [
                            { icon: "⏳", title: "Same Status Since (n) Days", desc: "Stagnant status tracking" },
                            { icon: "⏳", title: "After Assign Same Status Since (n) Days", desc: "Post-assignment delays" },
                            { icon: "⏳", title: "After RP Meetings Assign Same Status", desc: "Post-meeting follow-up" },
                            { icon: "⏳", title: "Same Status With Task Count (On RP Leads)", desc: "Task correlation analysis" },
                            { icon: "🏢", title: "Company Created Between Dates", desc: "New company analytics" },
                            { icon: "🚫", title: "Deleted Company Between Dates", desc: "Company deletion log" },
                            { icon: "🔄", title: "Funnel Transfer Logs", desc: "Lead transfer tracking" }
                        ]
                    },
                    {
                        title: "Pipeline & Sales Analytics",
                        cards: [
                            { icon: "🎯", title: "Closure PipeLine", desc: "Pipeline closure forecasting" },
                            { icon: "🚀", title: "Team Visit in Travel Cluster", desc: "Travel cluster visits" },
                            { icon: "🚫", title: "More Than N Days No Activity (Main BD)", desc: "BD inactivity alerts" },
                            { icon: "🚫", title: "More Than N Days No Activity (Line Manager)", desc: "Manager inactivity alerts" },
                            { icon: "🚫", title: "No Activity After RP Meeting", desc: "Post-meeting inactivity" },
                            { icon: "🚫", title: "No Activity After Proposal Sent", desc: "Post-proposal follow-up" }
                        ]
                    },
                    {
                        title: "Review & Planning",
                        cards: [
                            { icon: "📝", title: "Review Report", desc: "Performance review summaries" },
                            { icon: "📄", title: "Review Data Report", desc: "Detailed review analytics" },
                            { icon: "📆", title: "Annual Review Report", desc: "Yearly review analysis" },
                            { icon: "🗓️", title: "Planner Report", desc: "Planning and scheduling" },
                            { icon: "⚠️", title: "Today's Compulsive & Needs Attention", desc: "Priority alerts" },
                            { icon: "⚠️", title: "Today's Replanned Task", desc: "Rescheduled task tracking" },
                            { icon: "🗓️", title: "Team Planner Reports", desc: "Team planning overview" }
                        ]
                    },
                    {
                        title: "Time & Attendance",
                        cards: [
                            { icon: "🕒", title: "User App Usage Time", desc: "Application usage analytics" },
                            { icon: "⏱️", title: "Time Spent By User on App", desc: "User engagement metrics" },
                            { icon: "🏖️", title: "Leave Management", desc: "Leave request tracking" },
                            { icon: "📅", title: "Leave Management", desc: "Leave schedule management" },
                            { icon: "🌴", title: "Day Management", desc: "Daily activity tracking" },
                            { icon: "🌄", title: "Day Start", desc: "Day commencement logs" },
                            { icon: "🌅", title: "Day End", desc: "Day completion records" }
                        ]
                    },
                    {
                        title: "Sales & Team Management",
                        cards: [
                            { icon: "🌴", title: "Insides Sales Request", desc: "Internal sales requests" },
                            { icon: "👥", title: "BD Assign Request BY Insides Sales", desc: "BD assignment requests" },
                            { icon: "🌴", title: "Check Management", desc: "Verification process tracking" },
                            { icon: "🌄", title: "Day Check Management", desc: "Daily verification logs" },
                            { icon: "📝", title: "Team Task Check", desc: "Team task verification" },
                            { icon: "📝", title: "Team Status Change Task Check", desc: "Status change validation" },
                            { icon: "📧", title: "Team Email Task Validate", desc: "Email task verification" }
                        ]
                    },
                    {
                        title: "Rating & Location",
                        cards: [
                            { icon: "🌟", title: "Star Rating Report", desc: "Performance rating analysis" },
                            { icon: "🌞", title: "Day Check Star Rating Report", desc: "Daily rating summaries" },
                            { icon: "📋", title: "Team Task Check Star Rating Report", desc: "Task-based ratings" },
                            { icon: "📍", title: "Location", desc: "Location tracking" },
                            { icon: "📌", title: "Location", desc: "Location management" },
                            { icon: "✅", title: "MoM Approval Status", desc: "Meeting minutes approval" },
                            { icon: "📊", title: "MoM Status", desc: "Minutes status tracking" }
                        ]
                    },
                    {
                        title: "Documentation & Strategy",
                        cards: [
                            { icon: "📋", title: "MOM Detail", desc: "Minutes details and records" },
                            { icon: "📝", title: "Special Remarks", desc: "Special notes and comments" },
                            { icon: "📝", title: "Special Remarks Leads", desc: "Lead-specific remarks" },
                            { icon: "📝", title: "Quater Strategy", desc: "Quarterly strategy planning" },
                            { icon: "📝", title: "Quater Strategy Details", desc: "Detailed strategy breakdown" },
                            { icon: "🟢", title: "Live Task & Planner Management", desc: "Real-time task tracking" }
                        ]
                    },
                    {
                        title: "Live Operations",
                        cards: [
                            { icon: "📡", title: "Live Meeting", desc: "Real-time meeting tracking" },
                            { icon: "📡", title: "Live Call", desc: "Active call monitoring" },
                            { icon: "📡", title: "Live Email (AutoTask)", desc: "Automated email tracking" },
                            { icon: "📡", title: "Live Proposal", desc: "Proposal status monitoring" },
                            { icon: "📡", title: "Live Mom Check", desc: "Minutes verification live" },
                            { icon: "📡", title: "Live Proposal Check", desc: "Proposal verification live" },
                            { icon: "📡", title: "Live Review Check", desc: "Review monitoring live" }
                        ]
                    },
                    {
                        title: "Live Planning & Checks",
                        cards: [
                            { icon: "📡", title: "Live Todays Task Planner", desc: "Today's task monitoring" },
                            { icon: "📡", title: "Live Next Days Task Planner", desc: "Tomorrow's planning" },
                            { icon: "📡", title: "Live Day Check", desc: "Daily verification live" },
                            { icon: "📡", title: "Live Task Check", desc: "Task verification live" },
                            { icon: "🧳", title: "Travel Advance", desc: "Travel advance requests" },
                            { icon: "📌", title: "Request", desc: "General request tracking" }
                        ]
                    },
                    {
                        title: "Travel & Expense",
                        cards: [
                            { icon: "✈️", title: "Team Travel Advance Request", desc: "Team travel funding" },
                            { icon: "💰", title: "Team Cash Expense Request", desc: "Team expense claims" },
                            { icon: "📊", title: "Report", desc: "General reporting" },
                            { icon: "📑", title: "Team Travel Advance Report", desc: "Travel advance analytics" },
                            { icon: "🧾", title: "Team Cash Expense Report", desc: "Expense claim analysis" },
                            { icon: "👥", title: "Our Travel Advance", desc: "Personal travel advances" }
                        ]
                    },
                    {
                        title: "Finance & Meeting Management",
                        cards: [
                            { icon: "💵", title: "Our Cash Advance Request", desc: "Personal cash advances" },
                            { icon: "📝", title: "Our Cash Expense Request", desc: "Personal expense claims" },
                            { icon: "👥", title: "ADD Meetings Expense", desc: "Meeting expense logging" },
                            { icon: "📝", title: "Update Meetings Expense Details", desc: "Expense detail updates" },
                            { icon: "💰", title: "Advance vs 🤝 Meeting", desc: "Advance vs meeting cost" },
                            { icon: "📑", title: "Travel Advance – Meeting Overview", desc: "Travel-meeting correlation" }
                        ]
                    },
                    {
                        title: "Performance & Targets",
                        cards: [
                            { icon: "🎯", title: "Target V/S 🏆 Achievement", desc: "Target vs actual comparison" },
                            { icon: "📋", title: "Target List", desc: "Target listing and tracking" },
                            { icon: "📈", title: "Graph Analysis", desc: "Graphical data analysis" },
                            { icon: "💹", title: "Sales Graph", desc: "Sales performance graphs" },
                            { icon: "🧭", title: "Travel Cluster", desc: "Travel cluster management" }
                        ]
                    },
                    {
                        title: "Cluster & Handover",
                        cards: [
                            { icon: "👥", title: "Team Travel Cluster Details", desc: "Team cluster information" },
                            { icon: "🆕", title: "New Travel Cluster", desc: "New cluster creation" },
                            { icon: "🌍", title: "Our Travel Cluster Details", desc: "Personal cluster info" },
                            { icon: "✏️", title: "Team Travel Cluster Edit Request", desc: "Cluster edit requests" },
                            { icon: "📈", title: "Handover & BD Request", desc: "Handover and BD requests" }
                        ]
                    },
                    {
                        title: "Project Handover & Client",
                        cards: [
                            { icon: "🤝", title: "Handover Details", desc: "Project handover information" },
                            { icon: "🤝", title: "BD Request Details", desc: "Business development requests" },
                            { icon: "🤝", title: "Program Timeline Details", desc: "Program timeline tracking" },
                            { icon: "🤝", title: "Handover Timeline Details", desc: "Handover schedule" },
                            { icon: "🤝", title: "Operation Work on Our & Team Project", desc: "Operational work tracking" }
                        ]
                    },
                    {
                        title: "Client Onboarding",
                        cards: [
                            { icon: "🏆", title: "On Boarded Client", desc: "New client onboarding" },
                            { icon: "🤝", title: "Handover to Operation", desc: "Operation handover process" },
                            { icon: "🏗️", title: "Handover to Installation", desc: "Installation handover" },
                            { icon: "🎨", title: "Artwork Apr", desc: "Artwork approval process" },
                            { icon: "📊", title: "Total Handover", desc: "Complete handover summary" },
                            { icon: "📋", title: "On Boarded Client Detail", desc: "Client detail information" }
                        ]
                    }
                ];
                
                let html = '';
                
                exploreCategories.forEach(category => {
                    html += `
                        <div class="explore-category">
                            <h3>${category.title}</h3>
                            <div class="category-grid">
                                ${category.cards.map(card => `
                                    <div class="explore-card" data-report="${card.title}">
                                        <div style="font-size: 32px; margin-bottom: 10px;">${card.icon}</div>
                                        <h4>${card.title}</h4>
                                        <p>${card.desc}</p>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
                
                // Add event listeners to explore cards
                container.querySelectorAll('.explore-card').forEach(card => {
                    card.addEventListener('click', () => {
                        const reportName = card.dataset.report;
                        this.startReportAnalysis(reportName);
                    });
                });
            },
            
            // Start report analysis
            startReportAnalysis(reportName) {
                // Create new chat for this report
                const chatId = 'report_' + Date.now();
                const newChat = {
                    id: chatId,
                    title: reportName,
                    messages: [],
                    analysisType: reportName,
                    reportType: reportName,
                    createdAt: new Date().toISOString(),
                    updatedAt: new Date().toISOString()
                };
                
                this.state.conversations.unshift(newChat);
                this.state.currentChatId = chatId;
                this.state.currentAnalysisType = 'reportAnalysis';
                this.saveConversations();
                this.loadChat(chatId);
                
                // Show date range header
                this.showDateRangeHeader(reportName);
                
                // Set auto-prompt
                const prompt = `Generate ${reportName} report for ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}. Include key metrics, trends, and actionable insights.`;
                document.getElementById('chatInput').value = prompt;
                document.getElementById('chatInput').focus();
                
                // Auto-resize textarea
                const textarea = document.getElementById('chatInput');
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
            },
            
            // Bind event listeners
            bindEvents() {
                // New analysis button
                document.getElementById('newChatBtn').addEventListener('click', () => this.newAnalysis());
                
                // Navigation items
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        const view = item.dataset.view;
                        this.switchView(view);
                        
                        // Update active state
                        document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                        item.classList.add('active');
                    });
                });
                
                // Send message
                document.getElementById('sendBtn').addEventListener('click', () => this.sendMessage());
                
                // Enter key to send message
                document.getElementById('chatInput').addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.sendMessage();
                    }
                });
                
                // Auto-resize textarea
                document.getElementById('chatInput').addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
                
                // Date input changes
                document.getElementById('startDate').addEventListener('change', (e) => {
                    this.state.selectedDateRange.startDate = e.target.value;
                    this.updateQuickDateButtons();
                });
                
                document.getElementById('endDate').addEventListener('change', (e) => {
                    this.state.selectedDateRange.endDate = e.target.value;
                    this.updateQuickDateButtons();
                });
                
                // Quick date buttons
                document.getElementById('quickDateButtons').addEventListener('click', (e) => {
                    if (e.target.classList.contains('quick-date-btn')) {
                        const days = e.target.dataset.days;
                        this.setQuickDateRange(days);
                        
                        // Set active button immediately
                        this.setActiveQuickDateButton(days);
                    }
                });
                
                // Theme toggle
                document.getElementById('themeToggle').addEventListener('click', () => this.toggleTheme());
                
                // Upgrade button
                document.getElementById('upgradeBtn').addEventListener('click', () => this.showUpgradeModal());
                
                // Settings button
                document.getElementById('settingsBtn').addEventListener('click', () => this.showSettingsModal());
                
                // Fixed chat options
                document.addEventListener('click', (e) => {
                    if (e.target.closest('.fixed-chat-card')) {
                        const card = e.target.closest('.fixed-chat-card');
                        const analysisType = card.dataset.analysis;
                        this.startFixedAnalysis(analysisType);
                    }
                });
            },
            
            // Set active quick date button
            setActiveQuickDateButton(buttonId) {
                const buttons = document.querySelectorAll('.quick-date-btn');
                buttons.forEach(btn => btn.classList.remove('active'));
                
                const activeButton = document.querySelector(`.quick-date-btn[data-days="${buttonId}"]`);
                if (activeButton) {
                    activeButton.classList.add('active');
                }
                
                this.state.activeQuickDateBtn = buttonId;
            },
            
            // Set quick date range
            setQuickDateRange(days) {
                const today = new Date();
                let startDate = new Date();
                let endDate = today;
                
                switch(days) {
                    case '1': // Today
                        startDate = today;
                        break;
                    case '7': // Last 7 days
                        startDate.setDate(today.getDate() - 7);
                        break;
                    case '30': // Last 30 days (default)
                        startDate.setDate(today.getDate() - 30);
                        break;
                    case '90': // Last 90 days
                        startDate.setDate(today.getDate() - 90);
                        break;
                    case 'this_month': // This month
                        startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                        endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        break;
                    case 'last_month': // Last month
                        startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                        break;
                    case 'this_quarter': // This quarter
                        const quarter = Math.floor(today.getMonth() / 3);
                        startDate = new Date(today.getFullYear(), quarter * 3, 1);
                        endDate = new Date(today.getFullYear(), (quarter * 3) + 3, 0);
                        break;
                    case 'this_year': // This year
                        startDate = new Date(today.getFullYear(), 0, 1);
                        endDate = new Date(today.getFullYear(), 11, 31);
                        break;
                }
                
                this.state.selectedDateRange = {
                    startDate: startDate.toISOString().split('T')[0],
                    endDate: endDate.toISOString().split('T')[0]
                };
                
                this.updateDateInputs();
                this.setActiveQuickDateButton(days);
            },
            
            // Update date inputs with current date range
            updateDateInputs() {
                document.getElementById('startDate').value = this.state.selectedDateRange.startDate;
                document.getElementById('endDate').value = this.state.selectedDateRange.endDate;
            },
            
            // Update quick date buttons active state based on current date range
            updateQuickDateButtons() {
                const start = new Date(this.state.selectedDateRange.startDate);
                const end = new Date(this.state.selectedDateRange.endDate);
                const today = new Date();
                
                // Reset all buttons
                const buttons = document.querySelectorAll('.quick-date-btn');
                buttons.forEach(btn => btn.classList.remove('active'));
                
                // Check which button matches current range
                const diffDays = Math.floor((end - start) / (1000 * 60 * 60 * 24));
                const isSameDay = start.toDateString() === end.toDateString();
                
                // Check for Today
                if (isSameDay && start.toDateString() === today.toDateString()) {
                    this.setActiveQuickDateButton('1');
                    return;
                }
                
                // Check for Last N Days
                if (end.toDateString() === today.toDateString()) {
                    if (diffDays === 6) { // 7 days inclusive
                        this.setActiveQuickDateButton('7');
                        return;
                    } else if (diffDays === 29) { // 30 days inclusive
                        this.setActiveQuickDateButton('30');
                        return;
                    } else if (diffDays === 89) { // 90 days inclusive
                        this.setActiveQuickDateButton('90');
                        return;
                    }
                }
                
                // Check for This Month
                if (start.getDate() === 1 && 
                    start.getMonth() === today.getMonth() &&
                    start.getFullYear() === today.getFullYear() &&
                    end.getDate() === new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate()) {
                    this.setActiveQuickDateButton('this_month');
                    return;
                }
                
                // Check for Last Month
                const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1);
                if (start.getDate() === 1 && 
                    start.getMonth() === lastMonth.getMonth() &&
                    start.getFullYear() === lastMonth.getFullYear() &&
                    end.getDate() === new Date(today.getFullYear(), today.getMonth(), 0).getDate()) {
                    this.setActiveQuickDateButton('last_month');
                    return;
                }
                
                // Check for This Quarter
                const quarter = Math.floor(today.getMonth() / 3);
                const quarterStart = new Date(today.getFullYear(), quarter * 3, 1);
                const quarterEnd = new Date(today.getFullYear(), (quarter * 3) + 3, 0);
                
                if (start.getTime() === quarterStart.getTime() && 
                    end.getTime() === quarterEnd.getTime()) {
                    this.setActiveQuickDateButton('this_quarter');
                    return;
                }
                
                // Check for This Year
                const yearStart = new Date(today.getFullYear(), 0, 1);
                const yearEnd = new Date(today.getFullYear(), 11, 31);
                
                if (start.getTime() === yearStart.getTime() && 
                    end.getTime() === yearEnd.getTime()) {
                    this.setActiveQuickDateButton('this_year');
                    return;
                }
                
                // If no match, clear active state
                this.state.activeQuickDateBtn = null;
            },
            
            // Render fixed chat options
            renderFixedChatOptions() {
                const analysisTypes = this.config.analysisTypes;
                const container = document.querySelector('.empty-chat .fixed-chat-options');
                
                if (container) {
                    const icons = {
                        'Task Analysis': 'fa-tasks',
                        'Task Planning': 'fa-calendar-alt',
                        'Task Execution': 'fa-play-circle',
                        'Status Change': 'fa-exchange-alt',
                        'Funnel Analysis': 'fa-filter',
                        'Action Analysis': 'fa-bolt',
                        'Day in Day Out': 'fa-calendar-day',
                        'Efficiency & Productivity': 'fa-chart-line',
                        'Daily Performance': 'fa-chart-bar',
                        'BD Performance': 'fa-user-tie',
                        'CM Performance': 'fa-users-cog'
                    };
                    
                    const descriptions = {
                        'Task Analysis': 'Analyze task completion and efficiency',
                        'Task Planning': 'Plan and schedule upcoming tasks',
                        'Task Execution': 'Monitor task execution progress',
                        'Status Change': 'Track status changes and transitions',
                        'Funnel Analysis': 'Analyze conversion funnels',
                        'Action Analysis': 'Evaluate action effectiveness',
                        'Day in Day Out': 'Daily routine and activity analysis',
                        'Efficiency & Productivity': 'Measure team efficiency metrics',
                        'Daily Performance': 'Daily performance reports',
                        'BD Performance': 'Business Development metrics',
                        'CM Performance': 'Community Manager performance'
                    };
                    
                    let html = '';
                    for (const [key, value] of Object.entries(analysisTypes)) {
                        html += `
                            <div class="fixed-chat-card" data-analysis="${key}">
                                <i class="fas ${icons[value] || 'fa-chart-bar'}"></i>
                                <h4>${value}</h4>
                                <p>${descriptions[value] || 'Business analytics'}</p>
                            </div>
                        `;
                    }
                    container.innerHTML = html;
                }
            },
            
            // Start a fixed analysis type
            startFixedAnalysis(analysisType) {
                const analysisName = this.config.analysisTypes[analysisType];
                if (!analysisName) return;
                
                // Create new chat for this analysis
                const chatId = 'analysis_' + Date.now();
                const newChat = {
                    id: chatId,
                    title: analysisName,
                    messages: [],
                    analysisType: analysisName,
                    createdAt: new Date().toISOString(),
                    updatedAt: new Date().toISOString()
                };
                
                this.state.conversations.unshift(newChat);
                this.state.currentChatId = chatId;
                this.state.currentAnalysisType = analysisType;
                this.saveConversations();
                this.loadChat(chatId);
                
                // Show date range header
                this.showDateRangeHeader(analysisName);
                
                // Set context message based on analysis type
                const contextMessages = {
                    'taskAnalysis': `Generate ${analysisName} report for ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}`,
                    'taskPlanning': `Help me plan tasks for the upcoming week with priorities and resource allocation for ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}`,
                    'taskExecution': `Analyze current task execution status and identify blockers for ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}`,
                    'statusChange': `Show me status change trends and patterns from ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}`,
                    'funnelAnalysis': `Analyze our sales/marketing funnel conversion rates and identify drop-off points between ${this.state.selectedDateRange.startDate} and ${this.state.selectedDateRange.endDate}`,
                    'actionAnalysis': `Evaluate the effectiveness of recent actions and initiatives from ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}`,
                    'dayInDayOut': `Analyze daily routines and identify optimization opportunities for ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}`,
                    'efficiencyProductivity': `Measure team efficiency and productivity metrics between ${this.state.selectedDateRange.startDate} and ${this.state.selectedDateRange.endDate}`,
                    'dailyPerformance': `Generate a daily performance report with key metrics for ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}`,
                    'bdPerformance': `Analyze Business Development performance and lead conversion from ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}`,
                    'cmPerformance': `Evaluate Community Manager performance and engagement metrics for ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}`
                };
                
                const prompt = contextMessages[analysisType] || `Generate ${analysisName} report for ${this.state.selectedDateRange.startDate} to ${this.state.selectedDateRange.endDate}`;
                document.getElementById('chatInput').value = prompt;
                document.getElementById('chatInput').focus();
                
                // Auto-resize
                const textarea = document.getElementById('chatInput');
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
            },
            
            // Show date range header
            showDateRangeHeader(analysisName) {
                const container = document.getElementById('chatMessages');
                const headerHtml = `
                    <div class="analysis-header">
                        <h3>${analysisName}</h3>
                        <p>Analysis for the selected date range</p>
                        <div class="date-info">
                            <i class="fas fa-calendar"></i> 
                            ${this.formatDate(this.state.selectedDateRange.startDate)} 
                            to 
                            ${this.formatDate(this.state.selectedDateRange.endDate)}
                        </div>
                    </div>
                `;
                
                container.innerHTML = headerHtml;
            },
            
            // Format date for display
            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },
            
            // Create new analysis
            newAnalysis() {
                this.showWelcomeScreen();
                this.state.currentChatId = null;
                this.state.currentAnalysisType = null;
                document.getElementById('headerTitle').textContent = 'Business Analytics Dashboard';
            },
            
            // Load a chat by ID
            loadChat(chatId) {
                this.state.currentChatId = chatId;
                const chat = this.state.conversations.find(c => c.id === chatId);
                
                if (chat) {
                    this.renderMessages(chat.messages);
                    document.getElementById('headerTitle').textContent = chat.title;
                    this.switchView('chat');
                    this.state.currentAnalysisType = chat.analysisType || null;
                }
            },
            
            // Send message to CI3 backend
            async sendMessage() {
                const input = document.getElementById('chatInput');
                const message = input.value.trim();
                
                if (!message || this.state.isTyping) return;
                
                // Get current date range
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                
                if (!startDate || !endDate) {
                    alert('Please select both start and end dates.');
                    return;
                }
                
                // Update state with current date range
                this.state.selectedDateRange = { startDate, endDate };
                
                // Clear input
                input.value = '';
                input.style.height = '56px';
                
                // Create or get current chat
                let chat;
                if (!this.state.currentChatId) {
                    const chatId = 'chat_' + Date.now();
                    chat = {
                        id: chatId,
                        title: this.generateChatTitle(message),
                        messages: [],
                        analysisType: this.state.currentAnalysisType,
                        createdAt: new Date().toISOString(),
                        updatedAt: new Date().toISOString()
                    };
                    this.state.conversations.unshift(chat);
                    this.state.currentChatId = chatId;
                } else {
                    chat = this.state.conversations.find(c => c.id === this.state.currentChatId);
                }
                
                // Add user message with date context
                const userMessage = {
                    role: 'user',
                    content: message,
                    dateRange: this.state.selectedDateRange,
                    timestamp: new Date().toISOString()
                };
                
                chat.messages.push(userMessage);
                chat.updatedAt = new Date().toISOString();
                
                // Update chat title if it's the first message
                if (chat.messages.length === 1 && !chat.title.includes('Analysis')) {
                    chat.title = this.generateChatTitle(message);
                }
                
                this.saveConversations();
                
                // Show date range header if this is a new analysis
                if (chat.messages.length === 1 && this.state.currentAnalysisType) {
                    this.showDateRangeHeader(this.config.analysisTypes[this.state.currentAnalysisType] || 'Analysis');
                }
                
                this.renderMessages(chat.messages);
                
                // Show typing indicator
                this.showTypingIndicator();
                
                try {
                    // Call CI3 backend via AJAX with date range
                    const response = await this.callCI3Backend({
                        message: message,
                        analysisType: this.state.currentAnalysisType,
                        chatId: chat.id,
                        dateRange: this.state.selectedDateRange,
                        previousMessages: chat.messages.slice(-5) // Send last 5 messages for context
                    });
                    
                    // Add assistant message
                    const assistantMessage = {
                        role: 'assistant',
                        content: response.content,
                        data: response.data || null,
                        dateRange: this.state.selectedDateRange,
                        timestamp: new Date().toISOString()
                    };
                    
                    chat.messages.push(assistantMessage);
                    chat.updatedAt = new Date().toISOString();
                    
                    this.saveConversations();
                    this.renderMessages(chat.messages);
                    
                    // If response contains data, render it
                    if (response.data) {
                        this.renderDataVisualization(response.data, chat.id);
                    }
                    
                } catch (error) {
                    console.error('Error getting AI response:', error);
                    
                    // Add error message
                    const errorMessage = {
                        role: 'assistant',
                        content: 'I apologize, but I encountered an error processing your request. Please try again or contact support.',
                        timestamp: new Date().toISOString()
                    };
                    
                    chat.messages.push(errorMessage);
                    this.saveConversations();
                    this.renderMessages(chat.messages);
                } finally {
                    // Hide typing indicator
                    this.hideTypingIndicator();
                }
            },
            
            // Call CI3 backend via AJAX
            async callCI3Backend(requestData) {
                // This is where you call your CodeIgniter 3 controller
                // The URL should point to your CI3 controller method
                
                try {
                    const response = await fetch(this.config.apiBaseUrl + this.config.endpoints.chat, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest' // Important for CI3 to detect AJAX
                        },
                        body: JSON.stringify(requestData)
                    });
                    
                    if (!response.ok) {
                        throw new Error(`Server error: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    // Check for CI3 JSON response structure
                    if (data.success) {
                        return data.data;
                    } else {
                        throw new Error(data.message || 'Unknown error from server');
                    }
                    
                } catch (error) {
                    console.error('AJAX Error:', error);
                    
                    // Fallback to mock response if CI3 backend is not available
                    return this.getMockResponse(
                        requestData.message, 
                        requestData.analysisType,
                        requestData.dateRange
                    );
                }
            },
            
            // Mock response for demo (when CI3 backend is not available)
            getMockResponse(userMessage, analysisType, dateRange) {
                const startDate = dateRange?.startDate ? new Date(dateRange.startDate).toLocaleDateString() : 'selected period';
                const endDate = dateRange?.endDate ? new Date(dateRange.endDate).toLocaleDateString() : 'selected period';
                
                const analysisResponses = {
                    'Task Analysis': `Task Analysis Report (${startDate} to ${endDate}):\n\n• Total Tasks: 156\n• Completed: 132 (84.6%)\n• In Progress: 18 (11.5%)\n• Pending: 6 (3.8%)\n• Average Completion Time: 1.8 days\n• Top Performer: Team A (95% completion rate)\n• Recommendation: Address the 6 pending tasks that are blocking dependent work.`,
                    'Task Planning': `Task Planning for ${startDate} to ${endDate}:\n\n1. High Priority (6 tasks)\n   • System Upgrade - 2 days\n   • Client Presentation - 1 day\n   • Monthly Report - 0.5 day\n\n2. Medium Priority (12 tasks)\n   • Code Review - 1 day\n   • Team Training - 0.5 day\n\n3. Low Priority (8 tasks)\n   • Documentation - 1 day\n\nResource Allocation:\n• Team A: 3 high, 4 medium tasks\n• Team B: 3 high, 4 medium tasks\n• Support: 4 low priority tasks`,
                    'Funnel Analysis': `Sales Funnel Analysis (${startDate} to ${endDate}):\n\nStage Conversions:\n• Leads: 1,245\n• MQLs: 312 (25% conversion)\n• SQLs: 156 (50% conversion)\n• Opportunities: 78 (50% conversion)\n• Closed Won: 31 (39.7% conversion)\n\nOverall Funnel Efficiency: 2.49%\n\nDrop-off Analysis:\n• Largest drop: MQL to SQL (156 lost)\n• Recommendation: Improve lead qualification process`,
                    'Daily Performance': `Daily Performance Report (${startDate} to ${endDate}):\n\nKey Metrics:\n• Active Users: 12,458 (+3.2%)\n• New Signups: 345\n• Revenue: $12,458.75\n• Support Tickets: 45 (Avg response: 12min)\n• System Uptime: 99.97%\n\nTop Performers:\n1. John D. - 45 tasks completed\n2. Sarah M. - 38 tasks completed\n3. Mike R. - 32 tasks completed`,
                    'BD Performance': `Business Development Performance (${startDate} to ${endDate}):\n\n• New Leads Generated: 1,245\n• Meetings Scheduled: 312\n• Proposals Sent: 156\n• Deals Closed: 45\n• Total Revenue: $245,000\n• Average Deal Size: $5,444\n• Conversion Rate: 3.6%\n\nTop BD Rep: Jane Smith\n• Leads: 312\n• Closed: 15\n• Revenue: $85,000`,
                    'CM Performance': `Community Manager Performance (${startDate} to ${endDate}):\n\nEngagement Metrics:\n• Posts Created: 45\n• Comments/Replies: 312\n• Community Growth: +1,245 members\n• Engagement Rate: 4.2%\n• Response Time: 1.2 hours avg\n\nContent Performance:\n• Top Post: Product Update (1,245 likes)\n• Most Comments: Feature Request (89 comments)\n\nRecommendation: Increase video content by 25%`
                };
                
                const defaultResponse = `Analysis of "${userMessage}" for ${startDate} to ${endDate}:\n\nBased on the available data, here are the key insights:\n\n• Performance metrics are trending positively\n• Several areas show improvement opportunities\n• Recommended actions:\n  1. Review current processes\n  2. Implement targeted improvements\n  3. Monitor results weekly\n\nFor detailed analysis, please connect to the live database.`;
                
                return {
                    content: analysisResponses[analysisType] || defaultResponse,
                    data: null
                };
            },
            
            // Render data visualization
            renderDataVisualization(data, chatId) {
                const container = document.getElementById('chatMessages');
                
                // Example: If data contains table data
                if (data.tableData) {
                    const tableHtml = `
                        <div class="message assistant-msg">
                            <div class="message-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="message-content">
                                <div class="message-role">Data Analysis</div>
                                <div class="message-text">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                ${data.tableData.headers.map(h => `<th>${h}</th>`).join('')}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${data.tableData.rows.map(row => `
                                                <tr>
                                                    ${row.map(cell => `<td>${cell}</td>`).join('')}
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    container.innerHTML += tableHtml;
                }
                
                // Scroll to bottom
                container.scrollTop = container.scrollHeight;
            },
            
            // Generate chat title from first message
            generateChatTitle(message) {
                const words = message.split(' ').slice(0, 5).join(' ');
                return words.length > 30 ? words.substring(0, 30) + '...' : words;
            },
            
            // Delete a chat (kept for internal use, not exposed to UI)
            deleteChat(chatId) {
                this.state.conversations = this.state.conversations.filter(c => c.id !== chatId);
                
                if (this.state.currentChatId === chatId) {
                    this.state.currentChatId = null;
                    this.showWelcomeScreen();
                }
                
                this.saveConversations();
            },
            
            // Render messages in chat view
            renderMessages(messages) {
                const container = document.getElementById('chatMessages');
                
                if (!messages || messages.length === 0) {
                    this.showWelcomeScreen();
                    return;
                }
                
                const messagesHtml = messages.map(msg => `
                    <div class="message ${msg.role}-msg">
                        <div class="message-avatar">
                            ${msg.role === 'user' ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>'}
                        </div>
                        <div class="message-content">
                            <div class="message-role">${msg.role === 'user' ? 'You' : 'Business AI'}</div>
                            <div class="message-text">${this.escapeHtml(msg.content)}</div>
                            ${msg.dateRange ? `<div class="message-text"><small><i class="fas fa-calendar"></i> ${this.formatDate(msg.dateRange.startDate)} - ${this.formatDate(msg.dateRange.endDate)}</small></div>` : ''}
                            ${msg.data ? '<div class="message-text"><small>Data visualization available</small></div>' : ''}
                        </div>
                    </div>
                `).join('');
                
                container.innerHTML = messagesHtml;
                
                // Scroll to bottom
                container.scrollTop = container.scrollHeight;
            },
            
            // Show welcome screen
            showWelcomeScreen() {
                const container = document.getElementById('chatMessages');
                container.innerHTML = `
                    <div class="empty-chat">
                        <div class="chat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h1 class="chat-title">Business Analytics AI</h1>
                        <p class="chat-subtitle">Select an analysis type or ask about business performance, metrics, or data insights. Use the date picker below to select your analysis period.</p>
                        
                        <div class="date-range-selector">
                            <div class="date-input-group">
                                <label for="startDate"><i class="fas fa-calendar-alt"></i> Start Date</label>
                                <input type="date" id="startDate" class="date-input" value="${this.state.selectedDateRange.startDate}">
                            </div>
                            <div class="date-input-group">
                                <label for="endDate"><i class="fas fa-calendar-alt"></i> End Date</label>
                                <input type="date" id="endDate" class="date-input" value="${this.state.selectedDateRange.endDate}">
                            </div>
                        </div>
                        
                        <div class="quick-date-buttons" id="quickDateButtons">
                            <button class="quick-date-btn" data-days="1">Today</button>
                            <button class="quick-date-btn" data-days="7">Last 7 Days</button>
                            <button class="quick-date-btn active" data-days="30">Last 30 Days</button>
                            <button class="quick-date-btn" data-days="90">Last 90 Days</button>
                            <button class="quick-date-btn" data-days="this_month">This Month</button>
                            <button class="quick-date-btn" data-days="last_month">Last Month</button>
                            <button class="quick-date-btn" data-days="this_quarter">This Quarter</button>
                            <button class="quick-date-btn" data-days="this_year">This Year</button>
                        </div>
                        
                        <div class="fixed-chat-options">
                            <!-- Fixed chat options will be rendered here -->
                        </div>
                    </div>
                `;
                
                // Re-render fixed options
                this.renderFixedChatOptions();
                
                // Re-bind date events for welcome screen
                this.bindDateEventsForWelcomeScreen();
                
                // Update active button based on current date range
                this.updateQuickDateButtons();
                
                document.getElementById('headerTitle').textContent = "Business Analytics Dashboard";
            },
            
            // Bind date events for welcome screen
            bindDateEventsForWelcomeScreen() {
                const startDateInput = document.getElementById('startDate');
                const endDateInput = document.getElementById('endDate');
                const quickDateButtons = document.getElementById('quickDateButtons');
                
                if (startDateInput) {
                    startDateInput.addEventListener('change', (e) => {
                        this.state.selectedDateRange.startDate = e.target.value;
                        this.updateQuickDateButtons();
                    });
                }
                
                if (endDateInput) {
                    endDateInput.addEventListener('change', (e) => {
                        this.state.selectedDateRange.endDate = e.target.value;
                        this.updateQuickDateButtons();
                    });
                }
                
                if (quickDateButtons) {
                    quickDateButtons.addEventListener('click', (e) => {
                        if (e.target.classList.contains('quick-date-btn')) {
                            const days = e.target.dataset.days;
                            this.setQuickDateRange(days);
                            this.setActiveQuickDateButton(days);
                        }
                    });
                }
            },
            
            // Show typing indicator
            showTypingIndicator() {
                this.state.isTyping = true;
                const container = document.getElementById('chatMessages');
                const typingHtml = `
                    <div class="message assistant-msg">
                        <div class="message-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="message-content">
                            <div class="message-role">Business AI</div>
                            <div class="typing-indicator">
                                <span>Analyzing data for ${this.formatDate(this.state.selectedDateRange.startDate)} to ${this.formatDate(this.state.selectedDateRange.endDate)}</span>
                                <div class="typing-dots">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                container.innerHTML += typingHtml;
                container.scrollTop = container.scrollHeight;
                
                // Disable send button
                document.getElementById('sendBtn').disabled = true;
            },
            
            // Hide typing indicator
            hideTypingIndicator() {
                this.state.isTyping = false;
                const container = document.getElementById('chatMessages');
                const typingIndicator = container.querySelector('.typing-indicator');
                if (typingIndicator) {
                    typingIndicator.closest('.message').remove();
                }
                
                // Enable send button
                document.getElementById('sendBtn').disabled = false;
            },
            
            // Switch between views
            switchView(view) {
                this.state.currentView = view;
                
                // Hide all views
                document.getElementById('chatView').style.display = 'none';
                document.getElementById('projectsView').style.display = 'none';
                document.getElementById('gptsView').style.display = 'none';
                document.getElementById('exploreView').style.display = 'none';
                document.getElementById('libraryView').style.display = 'none';
                document.getElementById('logoCreatorView').style.display = 'none';
                
                // Show selected view
                switch(view) {
                    case 'chat':
                        document.getElementById('chatView').style.display = 'flex';
                        break;
                    case 'projects':
                        document.getElementById('projectsView').style.display = 'block';
                        document.getElementById('headerTitle').textContent = 'Analytics Projects';
                        break;
                    case 'gpts':
                        document.getElementById('gptsView').style.display = 'block';
                        document.getElementById('headerTitle').textContent = 'AI Analysis Models';
                        break;
                    case 'explore':
                        document.getElementById('exploreView').style.display = 'block';
                        document.getElementById('headerTitle').textContent = 'Explore Business Data';
                        // Re-render explore cards when switching to explore view
                        setTimeout(() => this.renderExploreCards(), 0);
                        break;
                    case 'library':
                        document.getElementById('libraryView').style.display = 'block';
                        document.getElementById('headerTitle').textContent = 'Reports Library';
                        break;
                    case 'logo-creator':
                        document.getElementById('logoCreatorView').style.display = 'block';
                        document.getElementById('headerTitle').textContent = 'Analytics Dashboard';
                        break;
                }
            },
            
            // Toggle theme
            toggleTheme() {
                this.state.isDarkMode = !this.state.isDarkMode;
                
                if (this.state.isDarkMode) {
                    document.body.style.backgroundColor = '#0a0a0a';
                    document.body.style.color = '#fff';
                    document.getElementById('themeToggle').innerHTML = '<i class="fas fa-sun"></i> Light mode';
                } else {
                    document.body.style.backgroundColor = '#fafafa';
                    document.body.style.color = '#000';
                    document.getElementById('themeToggle').innerHTML = '<i class="fas fa-moon"></i> Dark mode';
                }
            },
            
            // Show upgrade modal
            showUpgradeModal() {
                alert('Upgrade to Advanced Analytics for:\n\n• Real-time data integration\n• Predictive analytics\n• Custom dashboard creation\n• Advanced visualization tools\n• API access for external systems\n\nContact sales for enterprise pricing.');
            },
            
            // Show settings modal
            showSettingsModal() {
                alert('Settings:\n\n• Data Source: Connected\n• Auto-refresh: Enabled\n• Notifications: On\n• Export Format: PDF/Excel\n\nUse admin panel for advanced settings.');
            },
            
            // Utility: Escape HTML
            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        };
        
        // Initialize the controller when page loads
        document.addEventListener('DOMContentLoaded', () => {
            BusinessAnalyticsController.init();
        });
    </script>
</body>
</html>