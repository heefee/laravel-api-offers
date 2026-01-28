<template>
    <div class="auth-wrapper">
        <transition name="fade" mode="out-in">
            <!-- Loading state while auto-authenticating -->
            <div v-if="loading" class="loading-container">
                <div class="modern-card loading-card">
                    <div class="card-header-custom">
                        <div class="logo-area">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="shield-icon"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <div class="header-content">
                            <h1>Insurance Gateway</h1>
                            <p class="text-muted">Connecting to secure portal...</p>
                        </div>
                    </div>
                    <div class="card-body p-5">
                        <div class="loading-spinner">
                            <span class="spinner-border spinner-border-lg"></span>
                            <p class="loading-text">Authenticating...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error state if auto-authentication fails -->
            <div v-else-if="error && !token" class="login-container">
                <div class="modern-card">
                    <div class="card-header-custom error-header">
                        <div class="logo-area">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="shield-icon"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        </div>
                        <div class="header-content">
                            <h1>Connection Error</h1>
                            <p class="text-muted">Unable to authenticate</p>
                        </div>
                    </div>
                    <div class="card-body p-5">
                        <div class="error-toast mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <span>{{ error }}</span>
                        </div>
                        <button @click="autoAuthenticate" class="btn btn-brand w-100">
                            <span v-if="retrying" class="spinner-border spinner-border-sm me-2"></span>
                            {{ retrying ? 'Retrying...' : 'Try Again' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Insurance Quote Form once authenticated -->
            <insurance-quote-form
                v-else-if="token"
                :token="token"
                @back="resetAuth"
                class="form-container"
            />
        </transition>
    </div>
</template>

<script>
import InsuranceQuoteForm from './InsuranceQuoteForm.vue';

export default {
    name: 'AuthenticationForm',
    components: {
        InsuranceQuoteForm,
    },
    data() {
        return {
            token: null,
            expiresAt: null,
            refreshToken: null,
            loading: true,
            retrying: false,
            error: null,
        };
    },
    mounted() {
        // Auto-authenticate when component mounts
        this.autoAuthenticate();
    },
    methods: {
        async autoAuthenticate() {
            this.loading = true;
            this.error = null;

            try {
                const response = await fetch('/api/auto-auth', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    this.error = data.error || 'Auto-authentication failed. Please check API configuration.';
                    return;
                }

                this.token = data.token;
                this.expiresAt = data.expires_at;
                this.refreshToken = data.refresh_token;
                localStorage.setItem('token', data.token);
            } catch (err) {
                this.error = err.message || 'Network error. Please try again.';
            } finally {
                this.loading = false;
                this.retrying = false;
            }
        },
        resetAuth() {
            this.token = null;
            this.expiresAt = null;
            this.refreshToken = null;
            this.error = null;
            localStorage.removeItem('token');
            // Re-authenticate automatically
            this.autoAuthenticate();
        },
    },
};
</script>

<style scoped>
.auth-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #344491 0%, #bf9cf1 100%);
    padding: 20px;
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

.loading-container,
.login-container {
    width: 100%;
    display: flex;
    justify-content: center;
}

.modern-card {
    background: white;
    width: 100%;
    max-width: 420px;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.modern-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2);
}

.loading-card {
    text-align: center;
}

.card-header-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 30px;
    text-align: center;
    color: white;
}

.error-header {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.logo-area {
    display: flex;
    justify-content: center;
    margin-bottom: 16px;
}

.shield-icon {
    color: rgba(255, 255, 255, 0.95);
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
}

.header-content h1 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: white;
    letter-spacing: -0.5px;
}

.header-content .text-muted {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.95rem;
    margin-top: 6px;
}

.card-body {
    padding: 40px;
}

.loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
}

.spinner-border-lg {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
}

.loading-text {
    margin-top: 16px;
    color: #64748b;
    font-size: 1rem;
}

.btn-brand {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    cursor: pointer;
    margin-top: 10px;
}

.btn-brand:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-brand:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.error-toast {
    background-color: #fef2f2;
    color: #dc2626;
    padding: 14px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    border: 1px solid #fecaca;
    display: flex;
    align-items: center;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
