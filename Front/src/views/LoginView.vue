<template>
<v-container class="fill-height">
    <v-row>
        <v-col md="8" offset-md="2" lg="6" offset-lg="3" xl="4" offset-xl="4">
            <LoginForm @login="handleLogin" @error="handleError"/>
        </v-col>
    </v-row>
</v-container>

</template>

<script>
import LoginForm from '../components/LoginForm'
import { POSITION, useToast } from 'vue-toastification'
import CloseButton from '../components/CloseButton'

export default {
    components: {
        LoginForm,
        CloseButton
    },
    setup(props, context){
        const toast = useToast()
        const handleLogin = (accessToken) => {
            context.emit('login', accessToken)
            toast.info('Successfully logged in', {
                position: POSITION.BOTTOM_CENTER,
                timeout: 3000,
                closeButton: CloseButton
            })
        }
        const handleError = (error) => {
            toast.error(error,{
                position: POSITION.BOTTOM_CENTER,
                timeout: 3000,
                closeButton: CloseButton
            })
        }
        return { handleLogin, handleError }
    }
}
</script>

<style>

</style>