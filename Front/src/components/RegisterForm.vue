<template>
<v-card elevation="16" min-width="350px">
    <v-card-title class="text-h4">Register</v-card-title>
    <v-divider></v-divider>
    <v-card-text>
        <v-form @submit.prevent="handleSubmit">
            <v-text-field v-model="data.name" class="my-2" type="text" label="Name" :error-messages="inputErrors.name"></v-text-field>
            <v-text-field v-model="data.surname" class="mb-2" type="text" label="Surname" :error-messages="inputErrors.surname"></v-text-field>
            <v-text-field v-model="data.email" class="mb-2" type="email" label="Email" :error-messages="inputErrors.email"></v-text-field>
            <v-text-field v-model="data.password" class="mb-2" type="password" label="Password" :error-messages="inputErrors.password"></v-text-field>
            <v-text-field v-model="data.passwordConfirmation" type="password" class="mt-2" label="Confirm password"></v-text-field>
            <v-select v-model="data.statusId" class="mb-2" label="Status" :items="statuses" item-value="id" item-title="name" :error-messages="inputErrors.statusId"></v-select>
            <v-btn type="submit" block class="text-capitalize" color="blue-grey-lighten-5" variant="elevated" :loading="submitting" :disabled="submitting">Register</v-btn>
        </v-form>
    </v-card-text>
</v-card>
</template>
<script>
import { reactive, ref } from 'vue'
import axios from "axios"
import router from '@/router'
  export default { 
    props: ['statuses'],
    setup(props, context){
        const data = reactive({
            name: '',
            surname: '',
            email: '',
            password: '',
            passwordConfirmation: '',
            statusId: ''
        })
        const submitting = ref(false)
        const inputErrors = ref({})
        const handleSubmit = () => {
            submitting.value = true
            axios.post(`${process.env.VUE_APP_BACKEND_API}/users/register`, data)
                .then(() => {
                    inputErrors.value = {}
                    submitting.value = false
                    context.emit('register')
                }).catch((err) => {
                    data.password = ''
                    data.passwordConfirmation = ''
                    if(err.response?.status === 422)
                    {
                        submitting.value = false
                        inputErrors.value = err.response.data.errors
                    }
                    else console.log(err)
                })
        }
        return { data, submitting, inputErrors, handleSubmit }
    }
  }
</script>

<style>

</style>