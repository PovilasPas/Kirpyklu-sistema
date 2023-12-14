<template>
    <v-container>
        <v-dialog v-if="isManager" v-model="itemDialog.open" min-width="335px" width="750px" persistent no-click-animation>
            <v-card>
                <v-toolbar flat color="blue-grey-lighten-5" density="compact">
                    <v-toolbar-title class="text-grey-darken-3 text-h5">{{itemFormTitle}}</v-toolbar-title>
                    <template v-slot:append>
                        <v-btn icon="mdi-close" color="grey-darken-3" @click="handleItemModalClose"></v-btn>
                    </template>
                </v-toolbar>
                <v-divider></v-divider>
                <v-card-text class="pa-4">
                    <v-form @submit.prevent="handleItemModalSubmit">
                        <v-text-field v-model="itemDialog.item.name" type="text" label="Salon name" class="my-2" :error-messages="itemDialog.inputErrors.name"></v-text-field>
                        <v-text-field v-model="itemDialog.item.address" type="text" label="Address" class="mb-2" :error-messages="itemDialog.inputErrors.address"></v-text-field>
                        <v-textarea v-model="itemDialog.item.description" label="Description" class="mb-2" :error-messages="itemDialog.inputErrors.description" no-resize rows="7"></v-textarea>
                        <v-btn type="submit" block class="text-capitalize" color="blue-grey-lighten-5" variant="elevated" :loading="itemDialog.submitting" :disabled="itemDialog.submitting">{{itemButtonTitle}}</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
        
        <v-dialog v-if="isManager && hasEditableItem" v-model="deleteDialog.open" min-width="335px" width="750px" persistent no-click-animation>
            <v-card>
                <v-toolbar flat color="blue-grey-lighten-5" density="compact">
                    <v-toolbar-title class="text-grey-darken-3 text-h5">Delete a hair salon</v-toolbar-title>
                    <template v-slot:append>
                        <v-btn icon="mdi-close" color="grey-darken-3" @click="handleDeleteModalClose"></v-btn>
                    </template>
                </v-toolbar>
                <v-divider></v-divider>
                <v-card-title class="text-h5 text-center text-wrap">
                    Are you sure you want to delete this hair salon?
                </v-card-title>
                <v-card-actions class="justify-center">
                    <v-btn color="red-darken-4" class="text-capitalize" variant="elevated" size="large" @click="handleDeleteModalSubmit" :loading="deleteDialog.submitting" :disabled="deleteDialog.submitting">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-row>
            <v-col md="10" offset-md="1" lg="8" offset-lg="2">
                <v-card v-if="!loading && (city || manager)" color="blue-grey-lighten-5" style="min-width: 350px">
                    <v-data-table-virtual  :headers="headers" :items="items" v-model="expanded" show-expand no-data-text="No hair salons available" density="compact">

                        <template v-slot:top>
                            <v-toolbar flat color="blue-grey-lighten-5" density="compact">
                                <v-toolbar-title v-if="cityId" class="text-grey-darken-3">{{city.name}}'s hair salons:</v-toolbar-title>
                                <v-toolbar-title v-else class="text-grey-darken-3">Your hair salons:</v-toolbar-title>
                                <v-toolbar-items v-if="isManager && cityId">
                                    <v-btn icon="mdi-plus" color="grey-darken-3" @click="handleNewItemModalOpen"></v-btn>
                                </v-toolbar-items>
                            </v-toolbar>
                        </template>

                        <template v-if="managerId" v-slot:item.address="{ item }">
                            {{item.address}},<br> {{item.cityName}}
                        </template>

                        <template v-slot:item.actions="{ item, index }">
                            <div v-if="isManager && claims.sub === item.managerId.toString()">
                                <v-btn icon="mdi-pencil-circle-outline" variant="flat" size="small" @click="handleEditItemModalOpen(index)"></v-btn>
                                <v-btn icon="mdi-close-circle-outline" class="text-red-darken-4" variant="flat" size="small" @click="handleDeleteModalOpen(index)"></v-btn>
                            </div>
                        </template>

                        <template v-slot:expanded-row="{ columns, item, index }">
                            <tr>
                                <td :colspan="columns.length" class="py-2" style="word-break: break-word;">
                                    <span>
                                        <strong>Description:</strong> 
                                        <div>{{item.description}}</div>
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="isManager && claims.sub === item.managerId.toString()" class="hidden-md-and-up">
                                <td :colspan="columns.length">
                                    <span>
                                        <strong>Actions:</strong> 
                                        <v-btn icon="mdi-pencil-circle-outline" variant="flat" size="small" @click="handleEditItemModalOpen(index)"></v-btn>
                                        <v-btn icon="mdi-close-circle-outline" class="text-red-darken-4" variant="flat" size="small" @click="handleDeleteModalOpen(index)"></v-btn>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2" :colspan="columns.length">
                                    <v-btn block class="text-capitalize" variant="elevated" color="blue-grey-lighten-5" :to="{name: 'hairdresserList', params: {cityId: item.cityId, salonId: item.id}}">Available hairdressers</v-btn>
                                </td>
                            </tr>
                        </template>

                    </v-data-table-virtual>
                </v-card>
                <TableLoader v-else :headers="headers" />
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { computed, nextTick, reactive, ref } from 'vue'
import axios from 'axios'
import TableLoader from '../components/TableLoader.vue'
import CloseButton from '../components/CloseButton.vue'
import { jwtDecode } from 'jwt-decode'
import { POSITION, useToast } from 'vue-toastification'
import router from '@/router'
export default {
    props: ['cityId', 'managerId', 'token'],
    components: {
        TableLoader
    },
    setup(props, context){
        const toast = useToast()

        const city = ref(null)
        const manager = ref(null)
        
        const items = ref([])
        const loading = ref(true)
        const expanded = ref([])
        const itemDialog = reactive({
            item: {},
            inputErrors: {},
            index: -1,
            open: false,
            submitting: false
        })
        const deleteDialog = reactive({
            index: -1,
            item: {},
            open: false,
            submitting: false
        })

        const handleNewItemModalOpen = () => {
            itemDialog.open = true
        }
        const handleEditItemModalOpen = (index) => {
            itemDialog.index = index
            itemDialog.item = Object.assign({}, items.value[index])
            nextTick(() => {
                itemDialog.open = true
            })
        }
        const handleItemModalClose = () => {
            itemDialog.open = false
            nextTick(() => {
                itemDialog.index = -1
                itemDialog.item = {}
                itemDialog.inputErrors = {}
            })
        }
        const handleItemModalSubmit = () => {
            itemDialog.submitting = true
            if(itemDialog.index <= -1)
            {
                axios.post(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons`, itemDialog.item)
                    .then((res) => {
                        itemDialog.submitting = false
                        items.value.push(res.data)
                        handleItemModalClose()
                        toast.success('Successfully created a hair salon', {
                            position: POSITION.BOTTOM_CENTER,
                            timeout: 3000,
                            closeButton: CloseButton
                        })
                    }).catch((err) => {
                        itemDialog.inputErrors = {}
                        if(err.response?.status === 422) {
                            itemDialog.submitting = false
                            itemDialog.inputErrors = err.response.data.errors
                        }
                        else if(err.response?.status === 403){
                            itemDialog.submitting = false
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
                                    axios.post(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons`, itemDialog.item)
                                        .then((res) => {
                                            itemDialog.submitting = false
                                            items.value.push(res.data)
                                            handleItemModalClose()
                                            toast.success('Successfully created a hair salon', {
                                                position: POSITION.BOTTOM_CENTER,
                                                timeout: 3000,
                                                closeButton: CloseButton
                                            })
                                        }).catch((err) => {
                                            itemDialog.submitting = false
                                            if(err.response?.status === 422) {
                                                itemDialog.inputErrors = err.response.data.errors
                                            }
                                            else console.log(err)
                                        })
                                }).catch(() => {
                                    context.emit('invalidate')
                                    router.push({name: 'login'})
                                })
                        }
                        else {
                            itemDialog.submitting = false
                            console.log(err)
                        }
                    })
            }
            else
            {
                axios.put(`${process.env.VUE_APP_BACKEND_API}/cities/${itemDialog.item.cityId}/hair-salons/${itemDialog.item.id}`, itemDialog.item)
                    .then((res) => {
                        itemDialog.submitting = false
                        items.value[itemDialog.index] = res.data
                        handleItemModalClose()
                        toast.success('Successfully updated hair salon information', {
                            position: POSITION.BOTTOM_CENTER,
                            timeout: 3000,
                            closeButton: CloseButton
                        })
                    }).catch((err) => {
                        itemDialog.inputErrors = {}
                        if(err.response?.status === 422) {
                            itemDialog.submitting = false
                            itemDialog.inputErrors = err.response.data.errors
                        }
                        else if(err.response?.status === 403) {
                            itemDialog.submitting = false
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
                                    axios.put(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${itemDialog.item.id}`, itemFormTitle.item)
                                        .then((res) => {
                                            itemDialog.submitting = false
                                            items.value[itemDialog.index] = res.data
                                            handleItemModalClose()
                                            toast.success('Successfully updated hair salon information', {
                                                position: POSITION.BOTTOM_CENTER,
                                                timeout: 3000,
                                                closeButton: CloseButton
                                            })
                                        }).catch((err) => {
                                            itemDialog.submitting = false
                                            if(err.response?.status === 422) {
                                                itemDialog.inputErrors = err.response.data.errors
                                            }
                                            else console.log(err)
                                        })
                                }).catch(() => {
                                    context.emit('invalidate')
                                    router.push({name: 'login'})
                                })
                        }
                        else {
                            itemDialog.submitting = false
                            console.log(err)
                        }
                    })
            }
        }
        const handleDeleteModalOpen = (index) => {
            deleteDialog.index = index
            deleteDialog.item = Object.assign({}, items.value[index])
            nextTick(() => {
                deleteDialog.open = true
            })
        }
        const handleDeleteModalClose = () => {
            deleteDialog.open = false
            nextTick(() => {
                deleteDialog.item = {}
                deleteDialog.index = -1
            })
        }
        const handleDeleteModalSubmit = () => {
            deleteDialog.submitting = true
            axios.delete(`${process.env.VUE_APP_BACKEND_API}/cities/${deleteDialog.item.cityId}/hair-salons/${deleteDialog.item.id}`)
                .then(() => {
                    deleteDialog.submitting = false
                    items.value.splice(deleteDialog.index, 1)
                    handleDeleteModalClose()
                    toast.success('Successfully deleted a hair salon', {
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
                    else if(err.response.status === 401) {
                        axios.post(`${process.env.VUE_APP_BACKEND_API}/users/refresh`)
                            .then((res) => {
                                context.emit('refresh', res.data.accessToken)
                                axios.delete(`${process.env.VUE_APP_BACKEND_API}/cities/${deleteDialog.item.cityId}/hair-salons/${deleteDialog.item.id}`)
                                    .then(() => {
                                        deleteDialog.submitting = false
                                        items.value.splice(deleteDialog.index, 1)
                                        handleDeleteModalClose()
                                        toast.success('Successfully deleted a hair salon', {
                                            position: POSITION.BOTTOM_CENTER,
                                            timeout: 3000,
                                            closeButton: CloseButton
                                        })
                                    }).catch((err) => {
                                        deleteDialog.submitting = false
                                        if(err.response?.status === 403)
                                        {
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
                    else {
                        deleteDialog.submitting = false
                        console.log(err)
                    }
                })
        }

        const claims = computed(() => {
            if(props.token)
                return jwtDecode(props.token)
        })
        const isManager = computed(() => {
            if(!props.token)
                return false
            return claims.value.role === 1
        })
        const hasEditableItem = computed(() => {
            return isManager.value && items.value.filter((item) => item.managerId.toString() === claims.value.sub).length
        })
        const headers = computed(() => {
            const h = [
                {title: 'Salon name', value: 'name'},
                {title: 'Address', value: 'address'},
            ]
            if(hasEditableItem.value)
                h.push({title: 'Actions', key: 'actions', align: 'center d-none d-md-table-cell', sortable: false})
            return h
        })
        const itemFormTitle = computed(() => itemDialog.index <= -1 ? 'Add a hair salon' : 'Edit a hair salon')
        const itemButtonTitle = computed(() => itemDialog.index <= -1 ? 'Add' : 'Confirm')

        if(props.cityId)
        {
            axios.get(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}`)
                .then((res) => {
                    city.value = res.data
                    axios.get(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons`)
                        .then((res) => {
                            items.value = res.data
                            loading.value = false
                        })
                }).catch((err) => {
                    const to = router.currentRoute.value
                    if(err.response?.status === 404) {
                        router.replace({
                            name: 'notFound',
                            params: { pathMatch: to.path.split('/').slice(1) },
                            query: to.query,
                            hash: to.hash
                        })
                    }
                    else if(!err.response) {
                        toast.error('Server is currently offline', {
                            position: POSITION.BOTTOM_CENTER,
                            timeout: 3000,
                            closeButton: CloseButton
                        })
                    }
                })
        }
        else
        {
            const handleLoadErrors = (err) => {
                const to = router.currentRoute.value
                if(err.response?.status === 404) {
                    router.replace({
                        name: 'notFound',
                        params:{pathMatch: to.path.split('/').slice(1)},
                        query: to.query,
                        hash: to.hash
                    })
                    return true
                }
                else if(err.response?.status === 403) {
                    toast.error(err.response.data.message, {
                        position: POSITION.BOTTOM_CENTER,
                        timeout: 3000,
                        closeButton: CloseButton
                    })
                    return true
                }
                else if(!err.response) {
                    toast.error('Server is currently offline', {
                        position: POSITION.BOTTOM_CENTER,
                        timeout: 3000,
                        closeButton: CloseButton
                    })
                    return true
                }
                return false
            }
            axios.get(`${process.env.VUE_APP_BACKEND_API}/managers/${props.managerId}/hair-salons`)
                .then((res) => {
                    items.value = res.data
                    axios.get(`${process.env.VUE_APP_BACKEND_API}/users/${props.managerId}`)
                        .then((res) => {
                            manager.value = res.data
                            loading.value = false
                    })
                }).catch((err) => {
                    handleLoadErrors(err)
                    if(err.response?.status === 401) {
                        axios.post(`${process.env.VUE_APP_BACKEND_API}/users/refresh`)
                            .then((res) => {
                                context.emit('refresh', res.data.accessToken)
                                axios.get(`${process.env.VUE_APP_BACKEND_API}/managers/${props.managerId}/hair-salons`)
                                    .then((res) => {
                                        items.value = res.data
                                        axios.get(`${process.env.VUE_APP_BACKEND_API}/users/${props.managerId}`)
                                            .then((res) => {
                                                manager.value = res.data
                                                loading.value = false
                                            })
                                    }).catch((err) => {
                                        if(!handleLoadErrors(err)) console.log(err)
                                    })
                            }).catch(() => {
                                context.emit('invalidate')
                                router.replace({ name: 'login' })
                            })
                    }
                    else console.log(err)
                })
        }
        return { 
            headers, 
            city,
            manager,
            items, 
            loading, 
            expanded, 
            itemDialog, 
            deleteDialog, 
            handleNewItemModalOpen,
            handleEditItemModalOpen, 
            handleItemModalClose, 
            handleItemModalSubmit, 
            handleDeleteModalOpen,
            handleDeleteModalClose,
            handleDeleteModalSubmit,
            claims,
            isManager,
            hasEditableItem,
            itemFormTitle,
            itemButtonTitle
        }
    }
}
</script>

<style>

</style>