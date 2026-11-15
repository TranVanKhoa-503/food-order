    .auth-google { margin-bottom: 20px; }
    .auth-google-after-form { margin: 12px 0 0; }
    .auth-google-button {
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid #747775;
        border-radius: var(--radius-sm);
        background: #fff;
        color: #1f1f1f;
        text-decoration: none;
        font: 600 14px/1.4 Roboto, Arial, sans-serif;
        text-align: center;
    }
    .auth-google-button svg { flex: 0 0 20px; }
    .auth-google-button span { min-width: 0; overflow-wrap: anywhere; }
    .auth-google-button[href]:hover { background: #f8faff; }
    .auth-google-button:focus-visible { outline: 2px solid #1a73e8; outline-offset: 2px; }
    .auth-google-button:disabled {
        border-color: var(--border-color);
        background: #f8fafc;
        color: #64748b;
        cursor: not-allowed;
    }
    .auth-google-status {
        margin-top: 6px;
        color: #64748b;
        font-size: 13px;
        text-align: center;
    }
    .auth-google-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        color: #64748b;
        font-size: 13px;
        text-align: center;
    }
    .auth-google-divider::before, .auth-google-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border-color);
    }
