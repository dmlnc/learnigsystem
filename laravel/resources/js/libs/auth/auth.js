import axios from './axios';

export default function setAuthToken(token) {
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
}
