<template>
<v-container class="fill-height">
   <LoadingDialog v-model="loading" />
    <v-row v-if="!loading">
        <v-col md="8" offset-md="2" lg="6" offset-lg="3" xl="4" offset-xl="4">
            <RegisterForm :statuses="statuses" @register="handleRegister"/>
        </v-col>
    </v-row>
</v-container>
</template>

<script>
import RegisterForm from '../components/RegisterForm.vue'
import LoadingDialog from '../components/LoadingDialog.vue'
import CloseButton from '../components/CloseButton.vue'
import { ref } from 'vue'
import axios from 'axios'
import { POSITION, useToast } from 'vue-toastification'
import router from '@/router'
export default {
    components: {
        RegisterForm,
        LoadingDialog
    },
    setup(){
        const toast = useToast()

        const statuses = ref([])
        const loading = ref(true)

        const handleRegister = () => {
            router.push({name: 'login'})
            toast.info('Successfully registered', {
                position: POSITION.BOTTOM_CENTER,
                timeout: 3000,
                closeButton: CloseButton
            })
        }

        axios.get(`${process.env.VUE_APP_BACKEND_API}/statuses`)
            .then((res) => {
                statuses.value = res.data
                loading.value = false
            }).catch((err) => {
                if(!err.response) {
                    toast.error('Server is currently offline', {
                        position: POSITION.BOTTOM_CENTER,
                        timeout: 3000,
                        closeButton: CloseButton
                    })
                }
                else console.log(err)
            })
        return { statuses, loading, handleRegister}
    }
}
</script>

<style>

</style>