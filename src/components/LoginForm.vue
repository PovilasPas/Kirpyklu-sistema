<template>
    <v-card elevation="16" min-width="350px">
        <v-card-title>
            <div class="text-h4">Login</div>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
            <v-form @submit.prevent="handleSubmit">
                <v-text-field v-model="data.email" type="email" class="my-2" label="email" :error-messages="inputErrors.email"></v-text-field>
                <v-text-field v-model="data.password" type="password" class="mb-2" label="password" :error-messages="inputErrors.password"></v-text-field>
                <v-btn type="submit" block class="text-capitalize" color="blue-grey-lighten-5" variant="elevated" :loading="submitting" :disabled="submitting">Login</v-btn>
            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
import { reactive, ref } from 'vue'
import axios from 'axios'
export default {
    setup(props, context){
        const data = reactive({
            email: '',
            password: ''
        })
        const submitting = ref(false)
        const inputErrors = ref({})
        const handleSubmit = () => {
            submitting.value = true
            axios.post(`${process.env.VUE_APP_BACKEND_API}/users/login`, data)
                .then((res) => {
                    inputErrors.value = {}
                    const accessToken = res.data.accessToken
                    context.emit('login', accessToken)
                }).catch((err) => {
                    data.password = ''
                    inputErrors.value = ''
                    submitting.value = false
                    if(err.response?.status === 422)
                        inputErrors.value = err.response.data.errors
                    else if(err.response?.status === 401)
                        context.emit('error', err.response.data.message)
                    else if(!err.response)
                        context.emit('error', 'Server is currently offline')
                    else console.log(err)
                })
        }
        return {data, submitting, inputErrors, handleSubmit}
    }
}
</script>

<style>

</style>