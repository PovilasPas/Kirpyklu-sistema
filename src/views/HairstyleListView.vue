<template>
    <v-container>

        <v-dialog v-model="infoDialog.open" :activator="infoDialog.activator" min-width="335px" width="750px">
            <v-card>
                <v-toolbar flat color="blue-grey-lighten-5" density="compact">
                    <v-toolbar-title class="text-grey-darken-3 text-h5">"{{infoDialog.item.name}}" info</v-toolbar-title>
                        <template v-slot:append>
                            <v-btn icon="mdi-close" color="grey-darken-3" @click="handleInfoModalClose"></v-btn>
                        </template>
                </v-toolbar>                
                <v-img :src="infoDialog.item.image" :aspect-ratio="16/9" width="750px"></v-img>
                <v-divider></v-divider>
                <v-card-text class="pa-4">
                    <v-list>
                        <v-list-item>
                            <v-list-item-title><strong>Price:</strong> {{infoDialog.item.price}} &euro;</v-list-item-title>
                        </v-list-item>
                        <v-divider></v-divider>
                        <v-list-item>
                            <v-list-item-title><strong>Est. duration:</strong> {{infoDialog.item.estimatedTimeMin}} min</v-list-item-title>
                        </v-list-item>
                        <v-divider></v-divider>
                    </v-list>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog v-if="isOwner && hairdresser && hairdresser.isApproved" v-model="itemDialog.open" min-width="335px" width="750px" persistent no-click-animation>
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
                        <v-text-field v-model="itemDialog.item.name" type="text" label="hairstyle" class="my-2" :error-messages="itemDialog.inputErrors.name"></v-text-field>
                        <v-text-field v-model="itemDialog.item.price" type="text" label="price" class="mb-2" :error-messages="itemDialog.inputErrors.price">
                            <template v-slot:append-inner>
                                <span>€</span>
                            </template>
                        </v-text-field>
                        <v-file-input v-model="itemDialog.item.image" show-size prepend-icon="mdi-image" label="image" class="mb-2" :error-messages="itemDialog.inputErrors.image"></v-file-input>
                        <v-text-field v-model="itemDialog.item.estimatedTimeMin" type="text" label="est. duration" class="mb-2" :error-messages="itemDialog.inputErrors.estimatedTimeMin">
                            <template v-slot:append-inner>
                                <span>min</span>
                            </template>
                        </v-text-field>
                        <v-btn type="submit" block class="text-capitalize" color="blue-grey-lighten-5" variant="elevated" :loading="itemDialog.submitting" :disabled="itemDialog.submitting">{{itemButtonTitle}}</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog v-if="isOwner && hairdresser && hairdresser.isApproved" v-model="deleteDialog.open" min-width="335px" width="750px" persistent no-click-animation>
            <v-card>
                <v-toolbar flat color="blue-grey-lighten-5" density="compact">
                    <v-toolbar-title class="text-grey-darken-3 text-h5">Delete a hairstyle</v-toolbar-title>
                    <template v-slot:append>
                        <v-btn icon="mdi-close" color="grey-darken-3" @click="handleDeleteModalClose"></v-btn>
                    </template>
                </v-toolbar>
                <v-divider></v-divider>
                <v-card-title class="text-h5 text-center text-wrap">
                    Are you sure you want to delete this hairstyle?
                </v-card-title>
                <v-card-actions class="justify-center">
                    <v-btn color="red-darken-4" class="text-capitalize" variant="elevated" size="large" @click="handleDeleteModalSubmit" :loading="deleteDialog.submitting" :disabled="deleteDialog.submitting">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        
        <v-row>
            <v-col md="10" offset-md="1" lg="8" offset-lg="2">

                <v-card v-if="hairdresser && !loading" color="blue-grey-lighten-5" style="min-width: 350px">
                    <v-data-table-virtual :headers="headers" :items="items" no-data-text="No hairstyles available" density="compact">
                        <template v-slot:top>
                            <v-toolbar flat color="blue-grey-lighten-5" density="compact">
                                <v-toolbar-title class="text-grey-darken-3">{{hairdresser.name + ' ' + hairdresser.surname}}'s hairstyles:</v-toolbar-title>
                                <v-toolbar-items v-if="isOwner && hairdresser.isApproved">
                                    <v-btn icon="mdi-plus" color="grey-darken-3" @click="handleNewItemModalOpen"></v-btn>
                                </v-toolbar-items>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.information="{ index }">
                            <v-btn icon="mdi-information-outline" variant="flat" size="small" @click.stop="handleInfoModalOpen($event, index)"></v-btn>
                        </template>
                        <template v-slot:item.actions="{ index }">
                            <v-btn icon="mdi-pencil-circle-outline" variant="flat" size="small" @click.stop="handleEditItemModalOpen(index)"></v-btn>
                            <v-btn icon="mdi-close-circle-outline" class="text-red-darken-4" variant="flat" size="small" @click.stop="handleDeleteModalOpen(index)"></v-btn>
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
import router from '@/router'
import { POSITION, useToast } from 'vue-toastification'
export default {
    props: ['cityId', 'salonId', 'hairdresserId', 'token'],
    components: {
        TableLoader
    },
    setup(props) {
        const toast = useToast()

        const hairdresser = ref(null)
        const items = ref([])
        const loading = ref(true)
        const infoDialog = reactive({
            activator: null,
            item: null,
            open: false
        })
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

        const handleInfoModalOpen = (e, i) => {
            infoDialog.item = items.value[i]
            infoDialog.activator = e.target
            nextTick(() => {
                infoDialog.open = true
            })
        }
        const handleInfoModalClose = () => {
            infoDialog.open = false
        }

        const handleNewItemModalOpen = () => {
            itemDialog.open = true
        }
        const handleEditItemModalOpen = (index) => {
            itemDialog.index = index
            itemDialog.item = Object.assign({},items.value[index])
            itemDialog.item.image = null
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
        const getBase64Encoded = async (file) => {
            const reader = new FileReader()
            reader.readAsDataURL(file)
            await new Promise((resolve) => reader.onload = () => resolve())
            return reader.result
        }
        const handleItemModalSubmit = async () => {
            let rawImg;
            if(itemDialog.item.image)
            {
                rawImg = await getBase64Encoded(itemDialog.item.image[0])
            }
            const data = {
                name: itemDialog.item.name,
                price: itemDialog.item.price,
                image: rawImg,
                estimatedTimeMin: itemDialog.item.estimatedTimeMin
            }
            if(itemDialog.index <= -1) {
                itemDialog.submitting = true
                axios.post(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${props.hairdresserId}/hairstyles`, data)
                    .then((res) => {
                        itemDialog.submitting = false
                        items.value.push(res.data)
                        handleItemModalClose()
                        toast.success('Successfully added a hairstyle', {
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
                                    axios.post(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${props.hairdresserId}/hairstyles`, data)
                                        .then((res) => {
                                            itemDialog.submitting = false
                                            items.value.push(res.data)
                                            handleItemModalClose()
                                            toast.success('Successfully added a hairstyle', {
                                                position: POSITION.BOTTOM_CENTER,
                                                timeout: 3000,
                                                closeButton: CloseButton
                                            })
                                        }).catch((err) => {
                                            itemDialog.submitting = false
                                            if(err.response?.status === 422) {
                                                itemDialog.inputErrors = err.response.data.errors
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
                                    router.push( {name: 'login'} )
                                })
                        }
                        else console.log(err)
                    })
            }
            else
            {
                itemDialog.submitting = true
                axios.patch(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${props.hairdresserId}/hairstyles/${itemDialog.item.id}`, data)
                    .then((res) => {
                        itemDialog.submitting = false
                        items.value[itemDialog.index] = res.data
                        handleItemModalClose()
                        toast.success('Successfylly updated hairstyle information', {
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
                                    axios.patch(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${props.hairdresserId}/hairstyles/${itemDialog.item.id}`, data)
                                        .then((res) => {
                                            itemDialog.submitting = false
                                            items.value[itemDialog.index] = res.data
                                            handleItemModalClose()
                                            toast.success('Successfylly updated hairstyle information', {
                                                position: POSITION.BOTTOM_CENTER,
                                                timeout: 3000,
                                                closeButton: CloseButton
                                            })
                                        }).catch((err) => {
                                            itemDialog.submitting = false
                                            if(err.response?.status === 422) {
                                                itemDialog.inputErrors = err.response.data.errors
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
                                    router.push({ name: 'login' })
                                })
                        }
                        else console.log(err)
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
            axios.delete(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${props.hairdresserId}/hairstyles/${deleteDialog.item.id}`)
                .then(() => {
                    deleteDialog.submitting = false
                    items.value.splice(deleteDialog.index, 1)
                    handleDeleteModalClose()
                    toast.success('Successfully delete a hairstyle', {
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
                                axios.delete(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${props.hairdresserId}/hairstyles/${deleteDialog.item.id}`)
                                    then((res) => {
                                        deleteDialog.submitting = false
                                        items.value.splice(deleteDialog.index, 1)
                                        handleDeleteModalClose()
                                        toast.success('Successfully delete a hairstyle', {
                                            position: POSITION.BOTTOM_CENTER,
                                            timeout: 3000,
                                            closeButton: CloseButton
                                        })
                                    }).catch((err) => {
                                        deleteDialog.submitting = false
                                        if(err.result?.status === 403) {
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
                                router.push({ name: 'login' })
                            })
                    }
                    else console.log(err)
                })
        }

        const itemFormTitle = computed(() => itemDialog.index <= -1 ? 'Add a hairstyle' : 'Edit a hairstyle')
        const itemButtonTitle = computed(() => itemDialog.index <= -1 ? 'Add' : 'Confirm')
        const isOwner = computed(() => {
            if(!props.token)
                return false
            const claims = jwtDecode(props.token)
            return claims.role === 2 && claims.sub === props.hairdresserId
        })
        const headers = computed(() => {
            const h = [
                {title: 'Hairstyle', value: 'name'},
                {title: 'Information', key: 'information', align: 'center', sortable: false}
            ]
            if(isOwner.value && hairdresser.value && hairdresser.value.isApproved)
                h.push({title: 'Actions', key: 'actions', align: 'center', sortable: false})
            return h
        })

        axios.get(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${props.hairdresserId}`)
            .then((res) => {
                hairdresser.value = res.data
                axios.get(`${process.env.VUE_APP_BACKEND_API}/cities/${props.cityId}/hair-salons/${props.salonId}/hairdressers/${props.hairdresserId}/hairstyles`)
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

        return { 
            headers, 
            hairdresser, 
            items, 
            loading,  
            infoDialog, 
            itemDialog,
            deleteDialog,
            handleInfoModalOpen, 
            handleInfoModalClose, 
            handleNewItemModalOpen,
            handleEditItemModalOpen,
            handleItemModalClose,
            handleItemModalSubmit,
            handleDeleteModalOpen,
            handleDeleteModalClose,
            handleDeleteModalSubmit,
            itemFormTitle,
            itemButtonTitle,
            isOwner,
        }
    }
}
</script>

<style>

</style>