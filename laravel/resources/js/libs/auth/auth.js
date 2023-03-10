import axios from './axios';
import store from '@/store';

const authFunctions = {


    setAuthToken(token) {
        if (token) {
            // Apply authorization token to every request if logged in
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            // Store token in local storage
            localStorage.setItem('access_token', token);
        } else {
            // Delete auth header
            delete axios.defaults.headers.common['Authorization'];
            // Remove token from local storage
            localStorage.removeItem('access_token');
        }
    },

    setUser(user) {
        if (user) {
            this.setUserInStore(user);
            localStorage.setItem('user', JSON.stringify(user));
        } else {
            this.setUserInStore(null);
            localStorage.removeItem('user');
        }
    },

    setCompany(company) {
        if (company) {
            this.setCompanyInStore(company);
            localStorage.setItem('company', JSON.stringify(company));
        } else {
            this.setCompanyInStore(null);
            localStorage.removeItem('company');
        }
    },

    checkAuth() {
        if (!this.getAuthToken()) {
            this.resetAuth();
            return false;
        } else {
            this.setAuthToken(this.getAuthToken());
        }

        if (this.getUser() && this.getCompany()) {
            return true
        }

        this.resetAuth();
        return false;
    },

    resetAuth() {
        this.setAuthToken(false);
        this.setUser(false);
        this.setCompany(false);
    },

    getAuthToken() {
        const isAuthenticated = localStorage.getItem('access_token');
        return isAuthenticated;
    },

    getUser() {

        const userInStore = this.getUserFromStore();
        if (userInStore) {
            return userInStore;
        }

        const userInLocalStorage = localStorage.getItem('user');
        if (userInLocalStorage) {
            const parsedUser = JSON.parse(userInLocalStorage);

            this.setUserInStore(parsedUser);
            return parsedUser;
        }

        return null;
    },

    getCompany() {
        const companyInStore = this.getCompanyFromStore();
        if (companyInStore) {
            return companyInStore;
        }

        const companyInLocalStorage = localStorage.getItem('company');
        if (companyInLocalStorage) {
            const parsedCompany = JSON.parse(companyInLocalStorage);
            this.setCompanyInStore(parsedCompany);
            return parsedCompany;
        }

        return null;
    },

    setUserInStore(user) {
        store.dispatch('setUser', user);
    },

    setCompanyInStore(company) {
        store.dispatch('setCompany', company);
    },

    getUserFromStore() {
        return store.getters.getUser;
    },

    getCompanyFromStore() {
        return store.getters.getCompany;
    }


}
export default authFunctions
