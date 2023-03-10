import { createStore } from 'vuex'

const store = createStore({
  state: {
    user: null,
    company: null
  },
  mutations: {
    SET_USER(state, user) {
      state.user = user
    },
    SET_COMPANY(state, company) {
      state.company = company
    }
  },
  actions: {
    setUser({ commit }, user) {
      commit('SET_USER', user)
    },
    setCompany({ commit }, company) {
      commit('SET_COMPANY', company)
    }
  },
  getters: {
    getUser: state => state.user,
    getCompany: state => state.company
  }
})

export default store