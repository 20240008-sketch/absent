import { defineStore } from 'pinia';
import axios from 'axios';

export const useAdminStore = defineStore('admin', {
  state: () => ({
    classes: [],
    students: [],
    parents: [],
    loading: false,
    error: null
  }),

  actions: {
    // クラス一覧取得
    async fetchClasses(params = {}) {
      this.loading = true;
      try {
        const response = await axios.get('/api/admin/classes', { params });
        this.classes = response.data.data || response.data;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // クラス詳細取得
    async fetchClass(id) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/admin/classes/${id}`);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // クラス登録
    async createClass(data) {
      this.loading = true;
      try {
        const response = await axios.post('/api/admin/classes', data);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // クラス更新
    async updateClass(id, data) {
      this.loading = true;
      try {
        const response = await axios.put(`/api/admin/classes/${id}`, data);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // クラス削除
    async deleteClass(id) {
      this.loading = true;
      try {
        const response = await axios.delete(`/api/admin/classes/${id}`);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 生徒一覧取得
    async fetchStudents(params = {}) {
      this.loading = true;
      try {
        const response = await axios.get('/api/admin/students', { params });
        this.students = response.data.data || response.data;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 生徒詳細取得
    async fetchStudent(id) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/admin/students/${id}`);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 生徒登録
    async createStudent(data) {
      this.loading = true;
      try {
        const response = await axios.post('/api/admin/students', data);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 生徒更新
    async updateStudent(id, data) {
      this.loading = true;
      try {
        const response = await axios.put(`/api/admin/students/${id}`, data);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 生徒削除
    async deleteStudent(id) {
      this.loading = true;
      try {
        const response = await axios.delete(`/api/admin/students/${id}`);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 保護者一覧取得
    async fetchParents(params = {}) {
      this.loading = true;
      try {
        const response = await axios.get('/api/admin/parents', { params });
        this.parents = response.data.data || response.data;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 保護者詳細取得
    async fetchParent(id) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/admin/parents/${id}`);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 保護者登録
    async createParent(data) {
      this.loading = true;
      try {
        const response = await axios.post('/api/admin/parents', data);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 保護者更新
    async updateParent(id, data) {
      this.loading = true;
      try {
        const response = await axios.put(`/api/admin/parents/${id}`, data);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // 保護者削除
    async deleteParent(id) {
      this.loading = true;
      try {
        const response = await axios.delete(`/api/admin/parents/${id}`);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'エラーが発生しました';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // CSVインポート
    async importCsv(type, file) {
      this.loading = true;
      const formData = new FormData();
      formData.append('file', file);

      try {
        const response = await axios.post(`/api/admin/import/${type}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
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
