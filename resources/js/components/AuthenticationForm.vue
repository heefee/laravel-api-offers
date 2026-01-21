<template>
    <div class="auth-wrapper">
        <transition name="fade" mode="out-in">
            <insurance-quote-form
                v-if="token"
                :token="token"
                @back="resetAuth"
                class="form-container"
            />

            <div v-else class="login-container">
                <div class="modern-card">
                    <div class="card-header-custom">
                        <div class="logo-area">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="shield-icon"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <div class="header-content">
                            <h1>Insurance Gateway</h1>
                            <p class="text-muted">Secure Portal Access</p>
                        </div>
                    </div>

                    <div class="card-body p-5">
                        <form @submit.prevent="authenticate">
                            <div class="form-group mb-4">
                                <label for="account" class="form-label">Account ID</label>
                                <div class="input-group-custom">
                                    <span class="input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control custom-input"
                                        id="account"
                                        v-model="formData.account"
                                        placeholder="Enter your account number"
                                        required
                                        autocomplete="off"
                                    />
                                </div>
                            </div>

                            <div class="form-group mb-5">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group-custom">
                                    <span class="input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </span>
                                    <input
                                        type="password"
                                        class="form-control custom-input"
                                        id="password"
                                        v-model="formData.password"
                                        placeholder="••••••••"
                                        required
                                    />
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-brand w-100 mb-3"
                                :disabled="loading"
                            >
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                {{ loading ? 'Verifying...' : 'Sign In' }}
                            </button>
                        </form>

                        <transition name="slide-up">
                            <div v-if="error" class="error-toast">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <span>{{ error }}</span>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
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
            formData: {
                account: '',
                password: '',
            },
            token: null,
            expiresAt: null,
            refreshToken: null,
            loading: false,
            error: null,
        };
    },
    methods: {
        async authenticate() {
            this.loading = true;
            this.error = null;

            if (!this.formData.account.trim() || !this.formData.password.trim()) {
                this.error = 'Please fill in all fields';
                this.loading = false;
                return;
            }

            await new Promise(r => setTimeout(r, 500));

            try {
                const response = await fetch('/api/auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(this.formData),
                });

                const data = await response.json();

                if (!response.ok) {
                    this.error = data.error || 'Invalid credentials provided.';
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
            }
        },
        resetAuth() {
            this.token = null;
            this.expiresAt = null;
            this.refreshToken = null;
            this.formData.account = '';
            this.formData.password = '';
            this.error = null;
            localStorage.removeItem('token');
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

.card-header-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 30px;
    text-align: center;
    color: white;
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

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.input-group-custom {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    pointer-events: none;
}

.custom-input {
    width: 100%;
    padding: 12px 12px 12px 45px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    background-color: #f8fafc;
    transition: all 0.25s ease;
}

.custom-input:focus {
    border-color: #667eea;
    background-color: #fff;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
}

.custom-input::placeholder {
    color: #cbd5e1;
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

.slide-up-enter-active {
    transition: all 0.3s ease-out;
}

.slide-up-leave-active {
    transition: all 0.2s ease-in;
}

.slide-up-enter-from,
.slide-up-leave-to {
    transform: translateY(10px);
    opacity: 0;
}
</style>
