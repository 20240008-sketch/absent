import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    guard: null, // 'admin' or 'parent'
    isAuthenticated: false,
    needs2FA: false,
    email: null,
    needsPasswordChange: false
  }),

  getters: {
    isAdmin: (state) => state.guard === 'admin',
    isParent: (state) => state.guard === 'parent'
  },

  actions: {
    // 管理者ログイン
    async adminLogin(credentials) {
      try {
        const response = await axios.post('/api/admin/login', credentials);
        this.needs2FA = true;
        this.email = credentials.email;
        this.guard = 'admin';
        return response.data;
      } catch (error) {
        throw error;
      }
    },

    // 保護者ログイン
    async parentLogin(credentials) {
      try {
        const response = await axios.post('/api/parent/login', credentials);
        this.needs2FA = true;
        this.email = credentials.email;
        this.guard = 'parent';
        this.needsPasswordChange = response.data.needs_password_change || false;
        return response.data;
      } catch (error) {
        throw error;
      }
    },

    // 2FA検証
    async verify2FA(code) {
      try {
        const endpoint = this.guard === 'admin' 
          ? '/api/admin/verify-2fa' 
          : '/api/parent/verify-2fa';
        
        const response = await axios.post(endpoint, {
          email: this.email,
          code
        });

        this.user = response.data.parent || response.data.admin || response.data.user;
        this.isAuthenticated = true;
        this.needs2FA = false;
        
        if (response.data.needs_password_change !== undefined) {
          this.needsPasswordChange = response.data.needs_password_change;
        }

        return response.data;
      } catch (error) {
        throw error;
      }
    },

    // ログアウト
    async logout() {
      try {
        const endpoint = this.guard === 'admin' 
          ? '/api/admin/logout' 
          : '/api/parent/logout';
        
        await axios.post(endpoint);
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.user = null;
        this.guard = null;
        this.isAuthenticated = false;
        this.needs2FA = false;
        this.email = null;
        this.needsPasswordChange = false;
      }
    },

    // 現在のユーザー情報取得
    async fetchUser() {
      try {
        const endpoint = this.guard === 'admin' 
          ? '/api/admin/me' 
          : '/api/parent/me';
        
        const response = await axios.get(endpoint);
        this.user = response.data;
        this.isAuthenticated = true;
        
        if (response.data.needs_password_change !== undefined) {
          this.needsPasswordChange = response.data.needs_password_change;
        }

        return response.data;
      } catch (error) {
        this.user = null;
        this.isAuthenticated = false;
        throw error;
      }
    },

    // パスワード変更（保護者のみ）
    async changePassword(passwords) {
      try {
        const response = await axios.post('/api/parent/change-password', passwords);
        this.needsPasswordChange = false;
        return response.data;
      } catch (error) {
        throw error;
      }
    }
  }
});
