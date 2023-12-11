<template>
<div>
    <v-app-bar color="blue-grey-darken-3" elevation="16">
        <v-toolbar-title>
            <router-link :to="{ name: 'home' }" class="text-uppercase text-h5" style="text-decoration: none; color: inherit;">
                Hair salon app
            </router-link>
        </v-toolbar-title>
        <v-toolbar-items class="hidden-md-and-up">
            <v-btn icon="mdi-menu" @click.stop="handleDrawerClick"></v-btn>
        </v-toolbar-items>
        <v-toolbar-items v-if="token" class="hidden-sm-and-down">
            <v-btn v-if="parseInt(claims.role) === 1" prepend-icon="mdi-store" variant="text" class="text-capitalize" :to="{name: 'managerHairSalonList', params: {managerId: claims.sub}}" :active="false" size="large">Your salons</v-btn>
            <v-btn prepend-icon="mdi-account" variant="text" class="text-capitalize" :to="{name: 'user', params: {userId: claims.sub}}" :active="false" size="large">Your info</v-btn>
            <v-btn prepend-icon="mdi-logout" variant="text" class="text-capitalize" size="large" @click="handleLogoutClick" :loading="loggingOut" :disabled="loggingOut">Logout</v-btn>
        </v-toolbar-items>
        <v-toolbar-items v-else class="hidden-sm-and-down">
                <v-btn prepend-icon="mdi-login" variant="text" class="text-capitalize" :to="{name: 'login'}" :active="false" size="large">Login</v-btn>
                <v-btn prepend-icon="mdi-account-plus-outline" variant="text" class="text-capitalize" :to="{name: 'register'}" :active="false" size="large">Register</v-btn>
        </v-toolbar-items>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" location="right" temporary color="blue-grey-darken-3">
        <v-list v-if="token">
            <v-list-item v-if="claims.role === 1" prepend-icon="mdi-store" variant="text" :to="{name: 'managerHairSalonList', params: {managerId: claims.sub}}">
                <v-list-item-title>Your salons</v-list-item-title>
            </v-list-item>
            <v-list-item prepend-icon="mdi-account" variant="text" :to="{name: 'user', params: {userId: claims.sub}}">
                Your info
            </v-list-item>
            <v-list-item v-if="loggingOut" class="text-center">
                    <v-progress-circular indeterminate></v-progress-circular>
            </v-list-item>
            <v-list-item v-else prepend-icon="mdi-logout" variant="text" @click="handleLogoutClick">
                <v-list-item-title>Logout</v-list-item-title>
            </v-list-item>
        </v-list>
        <v-list v-else>
            <v-list-item prepend-icon="mdi-login" :to="{name: 'login'}" variant="text">
                <v-list-item-title>Login</v-list-item-title>
            </v-list-item>
            <v-list-item prepend-icon="mdi-account-plus-outline" :to="{name: 'register'}" variant="text">
                <v-list-item-title>Register</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-navigation-drawer>
</div>
</template>

<script>
import { computed, ref, watch } from 'vue'
import axios from 'axios'
import { jwtDecode } from 'jwt-decode'
import { useDisplay } from 'vuetify/lib/framework.mjs'
export default {
    props: ['token'],
    setup(props, context){
        const drawer = ref(false)
        const loggingOut = ref(false)
        const display = useDisplay()
        watch(display.mdAndUp, (newVal) => {
            if(newVal) drawer.value = false
        })
        const claims = computed(() => {
            if(props.token)
                return jwtDecode(props.token)
        })
        const handleDrawerClick = () => {
            drawer.value = !drawer.value
        }
        const handleLogoutClick = () => {
            loggingOut.value = true
            axios.post(`${process.env.VUE_APP_BACKEND_API}/users/logout`)
                .then(() => {
                    loggingOut.value = false
                    context.emit('logout')
                }).catch(() => {
                    loggingOut.value = false
                    context.emit('logout')
                })
        }


        return { drawer, loggingOut, claims, handleDrawerClick, handleLogoutClick }
    }
}
</script>

<style>

</style>