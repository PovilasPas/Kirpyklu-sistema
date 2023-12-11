
<template>
    <v-container class="fill-height">
        <LoadingDialog v-model="loading"/>
        <v-row v-if="!loading">
            <v-col class="pt-2 pb-2 d-flex align-center" cols="12">
                <p class="text-h4 me-1">Select a city</p>
                <v-icon class="ms-1 me-auto" icon="mdi-city" size="x-large"></v-icon>
            </v-col>
            <v-col class="pt-2 pb-2" cols="12">
                <v-divider thickness="2px"></v-divider>
            </v-col>
            <v-col class="pt-2 pb-2" v-for="city in cities" lg="4" md="6" cols="12" :key="city.id">
                <v-card variant="flat" color="blue-grey-lighten-5" :to="{name: 'hairSalonList', 'params': { cityId: city.id }}" hover>
                    <v-card-title class="text-grey-darken-3">{{ city.name }}</v-card-title>
                </v-card>
            </v-col>
            <v-col class="pt-2 pb-2" cols="12">
                <v-divider thickness="2px"></v-divider>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'
import LoadingDialog from '../components/LoadingDialog.vue'
import CloseButton from '../components/CloseButton.vue'
import { POSITION, useToast } from 'vue-toastification'
export default {
    components: {
        LoadingDialog
    },
    setup() {
        const toast = useToast()

        const cities = ref([])
        const loading = ref(true)
        axios.get(`${process.env.VUE_APP_BACKEND_API}/cities`)
            .then((res) => {
                cities.value = res.data
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
        return { cities, loading }
    }
}
</script>

<style>

</style>