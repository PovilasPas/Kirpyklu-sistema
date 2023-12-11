<template>
<v-container class="fill-height">

    <v-dialog v-model="deleteDialog.open" min-width="335px" width="750px" persistent no-click-animation>
        <v-card>
            <v-toolbar flat color="blue-grey-lighten-5" density="compact">
                <v-toolbar-title class="text-grey-darken-3 text-h5">Delete your account</v-toolbar-title>
                <template v-slot:append>
                    <v-btn icon="mdi-close" color="grey-darken-3" @click="handleDeleteModalClose"></v-btn>
                </template>
            </v-toolbar>
            <v-divider></v-divider>
            <v-card-title class="text-h5 text-center text-wrap">
                Are you sure you want to delete your account?
            </v-card-title>
            <v-card-actions class="justify-center">
                <v-btn color="red-darken-4" class="text-capitalize" variant="elevated" size="large" @click="handleDeleteModalSubmit" :loading="deleteDialog.submitting" :disabled="deleteDialog.submitting">Delete</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <LoadingDialog v-model="loading"/>
    <v-row v-if="!loading">
        <v-col md="8" offset-md="2" lg="6" offset-lg="3" xl="4" offset-xl="4">
            <UserForm :data="user.editedUser" :disabled="disabled" :submitting="submitting" :errors="inputErrors" @updateStart="handleUpdateStart" @dataUpdate="handledataUpdate" @updateCancel="handleUpdateCancel" @updateConfirm="handleUpdateConfirm" @delete="handleDeleteModalOpen"/>
        </v-col>
    </v-row>
</v-container>
</template>

<script>
import { reactive, ref } from 'vue'
import LoadingDialog from '../components/LoadingDialog.vue'
import UserForm from '../components/UserForm.vue'
import CloseButton from '../components/CloseButton.vue'
import axios from 'axios'
import router from '@/router'
import { POSITION, useToast } from 'vue-toastification'
export default {
    components: {
        LoadingDialog,
        UserForm
    },
    props: ['userId', 'token'],
    setup(props, context) {
        const toast = useToast()

        const loading = ref(true)
        const user = reactive({
            defaultUser: {},
            editedUser: {},
        })
        const disabled = ref(true)
        const submitting = ref(false)
        const inputErrors = ref({})

        const deleteDialog = reactive({
            open: false,
            submitting: false
        })

        const handleUpdateStart = () => {
            disabled.value = false
        }
        const handledataUpdate = (value) => {
            user.editedUser = value
        }
        const handleUpdateCancel = () => {
            disabled.value = true
            inputErrors.value = {}
            user.editedUser = Object.assign({}, user.defaultUser)
        }
        const handleUpdateConfirm = () => {
            submitting.value = true
            const data = {
                name: user.editedUser.name !== user.defaultUser.name ? user.editedUser.name : undefined,
                surname: user.editedUser.surname !== user.defaultUser.name ? user.editedUser.surname : undefined,
                email: user.editedUser.email !== user.defaultUser.email ? user.editedUser.email : undefined,
                password: user.editedUser.password ? user.editedUser.password : undefined,
                passwordConfirmation: user.editedUser.passwordConfirmation ? user.editedUser.passwordConfirmation : undefined
            }
            axios.patch(`${process.env.VUE_APP_BACKEND_API}/users/${props.userId}`, data)
                .then((res) => {
                    inputErrors.value = {}
                    submitting.value = false
                    disabled.value = true
                    user.defaultUser = res.data
                    user.editedUser = Object.assign({}, res.data)
                    toast.success('Successfuly updated user information', {
                        position: POSITION.BOTTOM_CENTER,
                        timeout: 3000,
                        closeButton: CloseButton
                    })
                }).catch((err) => {
                    inputErrors.value = {}
                    if(err.response?.status === 422) {
                        user.editedUser.password = undefined
                        user.editedUser.passwordConfirmation = undefined
                        submitting.value = false
                        inputErrors.value = err.response.data.errors
                    }
                    else if(err.response?.status === 403) {
                        user.editedUser.password = undefined
                        user.editedUser.passwordConfirmation = undefined
                        submitting.value = false
                        toast.error(err.response.data.message, {
                            position: POSITION.BOTTOM_CENTER,
                            timeout: 3000,
                            closeButton: CloseButton
                        })
                    }
                    else if(err.response?.status === 401) {
                        axios.post(`${process.env.VUE_APP_BACKEND_API}/users/refresh`)
                            .then((res) => {
                                context.emit(('refresh', res.data.accessToken))
                                axios.patch(`${process.env.VUE_APP_BACKEND_API}/users/${props.userId}`, data)
                                    .then((res) => {
                                        submitting.value = false
                                        disabled.value = true
                                        user.defaultUser = res.data
                                        user.editedUser = Object.assign({}, res.data)
                                        toast.success('Successfuly updated user information', {
                                            position: POSITION.BOTTOM_CENTER,
                                            timeout: 3000,
                                            closeButton: CloseButton
                                        })
                                    }).catch((err) => {
                                        user.editedUser.password = undefined
                                        user.editedUser.passwordConfirmation = undefined
                                        submitting.value = false
                                        if(err.response?.status === 422) {
                                            inputErrors.value = err.response.data.errors
                                        }
                                        else if(err.response?.status === 403) {
                                            toast.error(err.response.data.message, {
                                                position: POSITION.BOTTOM_CENTER,
                                                timeout: 3000,
                                                closeButton: CloseButton
                                            })
                                        }
                                        else console.log(err)
                                    })
                            }).catch(() => {
                                context.emit('invalidate')
                                router.push({name: 'login'})
                            })
                    }
                    else console.log(err)
                })
        }
        const handleDeleteModalOpen = () => {
            deleteDialog.open = true
        }
        const handleDeleteModalClose = () => {
            deleteDialog.open = false
        }
        const handleDeleteModalSubmit = () => {
            deleteDialog.submitting = true
            axios.delete(`${process.env.VUE_APP_BACKEND_API}/users/${props.userId}`)
                .then(() => {
                    deleteDialog.submitting = false
                    deleteDialog.open = false
                    context.emit('invalidate')
                    router.push({name: 'register'})
                    toast.success('Account was successfully removed', {
                        position: POSITION.BOTTOM_CENTER,
                        timeout: 3000,
                        closeButton: CloseButton
                    })
                }).catch((err) => {
                    if(err.response?.status === 403) {
                        deleteDialog.submitting = false
                        toast.error(err.response.data.message, {
                            position: POSITION.BOTTOM_CENTER,
                            timeout: 3000,
                            closeButton: CloseButton
                        })
                    }
                    else if(err.response?.status === 401) {
                        axios.post(`${process.env.VUE_APP_BACKEND_API}/users/refresh`)
                            .then((res) => {
                                context.emit('refresh', res.data.accessToken)
                                axios.delete(`${process.env.VUE_APP_BACKEND_API}/users/${props.userId}`)
                                    then(() => {
                                        deleteDialog.submitting = false
                                        deleteDialog.open = false
                                        context.emit('invalidate')
                                        router.push({name: 'register'})
                                        toast.success('Account was successfully removed', {
                                            position: POSITION.BOTTOM_CENTER,
                                            timeout: 3000,
                                            closeButton: CloseButton
                                        })
                                    }).catch((err) => {
                                        if(err.response?.status === 403) {
                                            deleteDialog.submitting = false
                                            toast.error(err.response.data.message, {
                                                position: POSITION.BOTTOM_CENTER,
                                                timeout: 3000,
                                                closeButton: CloseButton
                                            })
                                        }
                                        else console.log(err)
                                    })
                            }).catch(() => {
                                context.emit('invalidate')
                                router.push({name: 'login'})
                            })
                    }
                    else console.log(err)
                })
        }

        axios.get(`${process.env.VUE_APP_BACKEND_API}/users/${props.userId}`)
            .then((res) => {
                loading.value = false
                user.defaultUser = res.data
                user.editedUser = Object.assign({},res.data)
            }).catch((err) => {
                const to = router.currentRoute.value
                if(err.response?.status === 404) {
                    router.replace({
                        name: 'notFound',
                        params: {pathMatch: to.path.split('/').slice(1) },
                        query: to.query,
                        hash: to.hash
                    })
                }
                else if(err.response?.status === 403) {
                    toast.error(err.response.data.message, {
                        position: POSITION.BOTTOM_CENTER,
                        timeout: 3000,
                        closeButton: CloseButton
                    })
                }
                else if(err.response?.status === 401) {
                    axios.post(`${process.env.VUE_APP_BACKEND_API}/users/refresh`)
                        .then((res) => {
                            context.emit('refresh', res.data.accessToken)
                            axios.get(`${process.env.VUE_APP_BACKEND_API}/users/${props.userId}`)
                                .then((res) => {
                                    loading.value = false
                                    user.defaultUser = res.data
                                    user.editedUser = Object.assign({},res.data)
                                }).catch((err) => {
                                    if(err.response?.status === 404) {
                                        router.replace({
                                            name: 'notFound',
                                            params: {pathMatch: to.path.split('/').slice(1) },
                                            query: to.query,
                                            hash: to.hash
                                        })
                                    }
                                    else if(err.response?.status === 403) {
                                        toast.error(err.response.data.message, {
                                            position: POSITION.BOTTOM_CENTER,
                                            timeout: 3000,
                                            closeButton: CloseButton
                                        })
                                    }
                                    else if(!err.response) {
                                        toast.error('Server is currently offline', {
                                            position: POSITION.BOTTOM_CENTER,
                                            timeout: 3000,
                                            closeButton: CloseButton
                                        })
                                    }
                                    else console.log(err)
                                })
                        }).catch(() => {
                            context.emit('invalidate')
                            router.replace({ name: 'login' })
                        })
                }
                else if(!err.response) {
                    toast.error('Server is currently offline', {
                        position: POSITION.BOTTOM_CENTER,
                        timeout: 3000,
                        closeButton: CloseButton
                    })
                }
                else console.log(err)
            })

        return { 
            loading, 
            user, 
            disabled, 
            submitting, 
            inputErrors,
            deleteDialog,
            handleUpdateStart, 
            handledataUpdate, 
            handleUpdateCancel, 
            handleUpdateConfirm,
            handleDeleteModalOpen,
            handleDeleteModalClose,
            handleDeleteModalSubmit
        }
    }
}
</script>

<style>

</style>