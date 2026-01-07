import { defineStore } from 'pinia';
import axios from 'axios';

export const useParentStore = defineStore('parent', {
  state: () => ({
    absences: [],
    loading: false,
    error: null
  }),

  actions: {
    // 欠席連絡一覧取得
    async fetchAbsences(params = {}) {
      this.loading = true;
      try {
        const response = await axios.get('/api/parent/absences', { params });
        this.absences = response.data.data || response.data;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 欠席連絡詳細取得
    async fetchAbsence(id) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/parent/absences/${id}`);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 欠席連絡登録
    async createAbsence(data) {
      this.loading = true;
      try {
        const response = await axios.post('/api/parent/absences', data);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 欠席連絡更新
    async updateAbsence(id, data) {
      this.loading = true;
      try {
        const response = await axios.put(`/api/parent/absences/${id}`, data);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 欠席連絡削除
    async deleteAbsence(id) {
      this.loading = true;
      try {
        const response = await axios.delete(`/api/parent/absences/${id}`);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    }
  }
});
