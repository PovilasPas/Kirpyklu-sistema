<template>
  <v-app>
      <Navbar :token="token" @logout="handleLogout"/>
      <v-main>
          <router-view :token="token" :key="$route.fullPath" @login="handleLogin" @refresh="handleRefresh" @invalidate="handleInvalidate"/>
      </v-main>
      <Footer />
  </v-app>
</template>

<script>
import axios from 'axios'
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import CloseButton from './components/CloseButton.vue'
import router from './router'
import { ref } from 'vue'
import { POSITION, useToast } from 'vue-toastification'
export default {
  components: {
    Navbar,
    Footer
  },
  setup() {
    const toast = useToast()
    const token = ref(localStorage.getItem('token'))

    if(token)
      axios.defaults.headers.common['Authorization'] = 'Bearer ' + token.value

    const handleInvalidate = () => {
      axios.defaults.headers.common['Authorization'] = ''
      localStorage.clear()
      token.value = null
    }
    const handleLogout = () => {
      handleInvalidate()
      toast.info('Successfully logged out', {
        position: POSITION.BOTTOM_CENTER,
        timeout: 3000,
        closeButton: CloseButton
      })
      router.push({ name:'home' })
    }
    const handleLogin = (accessToken) => {
      axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken
      localStorage.setItem('token', accessToken)
      token.value = accessToken
      router.push({name: 'home'})
    }
    const handleRefresh = (accessToken) => {
      axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken
      localStorage.setItem('token', accessToken)
      token.value = accessToken
    }
    return {token, handleInvalidate, handleLogout, handleLogin, handleRefresh}
  }
}
</script>


<style>
.v-table > .v-table__wrapper > table > tbody > tr > th, .v-table > .v-table__wrapper > table > thead > tr > th, .v-table > .v-table__wrapper > table > tfoot > tr > th{
    font-weight: bold !important;
}
</style>
