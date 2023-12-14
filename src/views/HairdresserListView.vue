<template>
    <v-container>

        <v-dialog v-if="isManager || isHairdresser" v-model="itemDialog.open" min-width="335px" width="750px" persistent no-click-animation>
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
                        <div v-if="isManager">
                            <v-checkbox v-model="itemDialog.item.isApproved" label="Is approved" class="my2" :error-messages="itemDialog.inputErrors.isApproved" :false-value="0" :true-value="1"></v-checkbox>
                            <v-select v-model="itemDialog.item.hairSalonId" label="Hair salon" :items="salons" item-value="id" item-title="name" :error-messages="itemDialog.inputErrors.hairSalonId"></v-select>
                        </div>
                        <div v-else>
                            <v-text-field v-model="itemDialog.item.phoneNr" type="tel" label="Phone nr." class="my-2" :error-messages="itemDialog.inputErrors.phoneNr"></v-text-field>
                        </div>
                        <v-btn type="submit" block class="text-capitalize" color="blue-grey-lighten-5" variant="elevated" :loading="itemDialog.submitting" :disabled="itemDialog.submitting">{{itemButtonTitle}}</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog v-if="isManager || hasEditableItem" v-model="deleteDialog.open" min-width="335px" width="750px" persistent no-click-animation>
            <v-card>
                 <v-toolbar flat color="blue-grey-lighten-5" density="compact">
                    <v-toolbar-title class="text-grey-darken-3 text-h5">Delete a hairdresser</v-toolbar-title>
                    <template v-slot:append>
                        <v-btn icon="mdi-close" color="grey-darken-3" @click="handleDeleteModalClose"></v-btn>
                    </template>
                </v-toolbar>
                <v-divider></v-divider>
                <v-card-title class="text-h5 text-center text-wrap">
                    Are you sure you want to delete this hairdresser?
                </v-card-title>
                <v-card-actions class="justify-center">
                    <v-btn color="red-darken-4" class="text-capitalize" variant="elevated" size="large" @click="handleDeleteModalSubmit" :loading="deleteDialog.submitting" :disabled="deleteDialog.submitting">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-row>
            <v-col md="10" offset-md="1" lg="8" offset-lg="2">
                <v-card v-if="salon && !loading" color="blue-grey-lighten-5" min-width="350px">
                    <v-data-table-virtual :headers="headers" :items="items" v-model="expanded" show-expand no-data-text="No hairdressers available" density="compact">

                        <template v-slot:top>
                            <v-toolbar flat color="blue-grey-lighten-5" density="compact">
                                <v-toolbar-title class="text-grey-darken-3">{{salon.name}}'s hairdressers:</v-toolbar-title>
                                <v-toolbar-items v-if="isHairdresser">
                                    <v-btn icon="mdi-plus" color="grey-darken-3" @click="handleNewItemModalOpen"></v-btn>
                                </v-toolbar-items>
                            </v-toolbar>
                        </template>

                        <template v-slot:item.actions="{ item, index }">
                            <div v-if="isManager || isHairdresser && claims.sub === item.id.toString()">
                                <v-btn icon="mdi-pencil-circle-outline" variant="flat" size="small" @click="handleEditItemModalOpen(index)"></v-btn>
                                <v-btn icon="mdi-close-circle-outline" class="text-red-darken-4" variant="flat" size="small" @click="handleDeleteModalOpen(index)"></v-btn>
                            </div>
                        </template>

                        <template v-slot:item.isApproved="{ item }">
                            <div v-if="isManager || isHairdresser && claims.sub === item.id.toString()">
                                {{item.isApproved ? 'yes' : 'no'}}
                            </div>
                        </template>
                        <template v-slot:expanded-row="{ columns, item, index }">
                            <tr>
                                <td :colspan="columns.length">
                                    <span>
                                        <strong>Phone number:</strong> {{item.phoneNr}}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td :colspan="columns.length">
                                    <span>
                                        <strong>Email:</strong> {{item.email}}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="isManager || isHairdresser && claims.sub === item.id.toString()" class="hidden-md-and-up">
                                <td :colspan="columns.length">
                                    <span>
                                        <strong>Is approved:</strong> {{item.isApproved ? 'yes' : 'no'}}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="isManager || isHairdresser && claims.sub === item.id.toString()" class="hidden-md-and-up">
                                <td :colspan="columns.length">
                                    <span>
                                        <strong>Actions:</strong>
                                        <v-btn icon="mdi-pencil-circle-outline" variant="flat" size="small" @click="handleEditItemModalOpen(index)"></v-btn>
                                        <v-btn icon="mdi-close-circle-outline" class="text-red-darken-4" variant="flat" size="small" @click="handleDeleteModalOpen(index)"></v-btn>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td :colspan="columns.length" class="px-3 py-2">
                                    <v-btn block class="text-capitalize" variant="elevated" color="blue-grey-lighten-5" :to="{name: 'hairstyleList', params: {cityId: cityId, salonId: salonId, hairdresserId: item.id}}">Hairstyles</v-btn>
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
import { computed, nextTick, reactive, ref, watch } from 'vue'
import axios from 'axios'
import TableLoader from '../components/TableLoader.vue'
import { POSITION, useToast } from 'vue-toastification'
import CloseButton from '../components/CloseButton.vue'
import { jwtDecode } from 'jwt-decode'
import router from '@/router'
export default {
    props: ['cityId', 'salonId', 'token'],
    components:{
        TableLoader
    },
    setup(props, context){
        const toast = useToast()
        const salon = ref(null)
        const salons = ref([])
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
                axios.post(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers`, itemDialog.item)
                    .then((res) => {
                        itemDialog.submitting = false
                        items.value.push(res.data)
                        handleItemModalClose()
                        toast.success('Successfully added hairdresser information', {
                            position: POSITION.BOTTOM_CENTER,
                            timeout: 3000,
                            closeButton: CloseButton
                        })
                    }).catch((err) => {
                        itemDialog.inputErrors = {}
                        if(err.response?.status === 422)
                        {
                            itemDialog.submitting = false
                            itemDialog.inputErrors = err.response.data.errors
                        }
                        else if(err.response?.status === 409)
                        {
                            itemDialog.submitting = false
                            toast.error(err.response.data.message, {
                                position: POSITION.BOTTOM_CENTER,
                                timeout: 3000,
                                closeButton: CloseButton
                            })
                        }
                        else if(err.response?.status === 403) {
                            itemDialog.submitting = false
                                toast.error(err.response.data.message, {
                                position: POSITION.BOTTOM_CENTER,
                                timeout: 3000,
                                closeButton: CloseButton
                            })
                        }
                        else if(err.response?.status === 401)
                        {
                            axios.post(`${process.env.VUE_APP_BACKEND_API}/users/refresh`)
                                .then((res) => {
                                    context.emit('refresh', res.data.accessToken)
                                    axios.post(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers`, itemDialog.item)
                                        .then((res) => {
                                            itemDialog.submitting = false
                                            items.value.push(res.data)
                                            handleItemModalClose()
                                        }).catch((err) => {
                                            itemDialog.submitting = false
                                            if(err.response?.status === 422)
                                                itemDialog.inputErrors = err.response.data.errors
                                            else if(err.response?.status === 409)
                                            {
                                                toast.error(err.response.data.message, {
                                                    position: POSITION.BOTTOM_CENTER,
                                                    timeout: 3000,
                                                    closeButton: CloseButton
                                                })
                                            }
                                            else if(err.response?.status === 403) {
                                                itemDialog.submitting = false
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
            else
            {
                axios.put(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${itemDialog.item.id}`, itemDialog.item)
                    .then((res) => {
                        itemDialog.submitting = false
                        if(res.data.hairSalonId === parseInt(props.salonId))
                            items.value[itemDialog.index] = res.data
                        else 
                            items.value.splice(itemDialog.index, 1)
                        handleItemModalClose()
                        toast.success('Successfully updated hairdresser information',{
                            position: POSITION.BOTTOM_CENTER,
                            timeout: 3000,
                            closeButton: CloseButton
                        })
                    }).catch((err) => {
                        itemDialog.inputErrors = {}
                        if(err.response?.status === 422)
                        {
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
                                    axios.put(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${itemDialog.item.id}`, itemDialog.item)
                                        .then((res) => {
                                            itemDialog.submitting = false
                                            if(res.data.hairSalonId === parseInt(props.salonId))
                                                items.value[itemDialog.index] = res.data
                                            else 
                                                items.value.splice(itemDialog.index, 1)
                                            handleItemModalClose()
                                            toast.success('Successfully updated hairdresser information',{
                                                position: POSITION.BOTTOM_CENTER,
                                                timeout: 3000,
                                                closeButton: CloseButton
                                            })
                                        }).catch((err) => {
                                            itemDialog.submitting = false
                                            if(err.response?.status === 422)
                                                itemDialog.inputErrors = err.response.data.errors
                                            else if(err.response?.status === 403) {
                                                itemDialog.submitting = false
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
                        else console.log(err.response)
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
                deleteDialog.item = {},
                deleteDialog.index = -1 
            })
        }
        const handleDeleteModalSubmit = () => {
            deleteDialog.submitting = true
            axios.delete(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${deleteDialog.item.id}`)
                .then(() => {
                    deleteDialog.submitting = false
                    items.value.splice(deleteDialog.index, 1)
                    handleDeleteModalClose()
                    toast.success('Successfully deleted hairdresser information', {
                        position: POSITION.BOTTOM_CENTER,
                        timeout: 3000,
                        closeButton: CloseButton
                    })
                }).catch((err) => {
                    if(err.response?.status === 403) {
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
                                axios.delete(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${deleteDialog.item.id}`)
                                    .then(() => {
                                        deleteDialog.submitting = false
                                        items.value.splice(deleteDialog.index, 1)
                                        handleDeleteModalClose()
                                         toast.success('Successfully deleted hairdresser information', {
                                            position: POSITION.BOTTOM_CENTER,
                                            timeout: 3000,
                                            closeButton: CloseButton
                                        })
                                    }).catch((err) => {
                                        deleteDialog.submitting = false
                                        console.log(err)
                                    })
                            }).catch(() => {
                                context.emit('invalidate')
                                router.push({name: 'login'})
                            })
                    }
                    else console.log(err)
                })
        }
        const claims = computed(() => {
            if(props.token)
                return jwtDecode(props.token)
        })
        const isManager = computed(() => {
            if(!props.token)
                return false
            return claims.value.role === 1 && salon.value && parseInt(claims.value.sub) === salon.value.managerId
        })
        const isHairdresser = computed(() => {
            if(!props.token)
                return false
            return claims.value.role === 2
        })
        const hasEditableItem = computed(() => {
            return isHairdresser.value && items.value.filter((item) => item.id.toString() === claims.value.sub).length
        })
        const headers = computed(() => {
            const h = [
                {title: 'Hairdresser name', value: 'name'},
                {title: 'Hairdresser surname', value: 'surname'}
            ]
            if(isManager.value || hasEditableItem.value) {
                h.push({title: 'Is approved', key: 'isApproved', align: ' d-none d-md-table-cell', sortable: false})
                h.push({title: 'Actions', key: 'actions', align: 'center d-none d-md-table-cell', sortable: false})
            }
            return h
        })

        const itemFormTitle = computed(() => itemDialog.index <= -1 ? 'Become a hairdresser' : 'Edit hairdresser info')
        const itemButtonTitle = computed(() => itemDialog.index <= -1 ? 'Add' : 'Confirm')
        //slmb
        axios.get(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}`)
            .then((res) => {
                salon.value = res.data
                if(isManager.value) {
                    axios.get(`${process.env.VUE_APP_BACKEND_API}/managers/${claims.value.sub}/hair-salons`)
                        .then((res) => {
                            salons.value = res.data
                        }).catch((err) => {
                            if(err.response?.status === 401) {
                                axios.post(`${process.env.VUE_APP_BACKEND_API}/users/refresh`)
                                    .then((res) => {
                                        context.emit('refresh', res.data.accessToken)
                                        axios.get(`${process.env.VUE_APP_BACKEND_API}/managers/${claims.value.sub}/hair-salons`)
                                            .then((res) => {
                                                salons.value = res.data
                                            })
                                    }).catch(() => {
                                        context.emit('invalidate')
                                    })
                            }
                        })
                }
                axios.get(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers`)
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
                else console.log(err)
            })
        return { 
            toast,
            salon,
            salons,
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
            isHairdresser,
            hasEditableItem,
            headers,
            itemFormTitle,
            itemButtonTitle
        }
    }
}
</script>

<style>

</style>