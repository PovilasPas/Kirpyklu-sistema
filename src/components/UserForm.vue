<template>
  <v-card elevation="16" min-width="350px">
    <v-card-title class="d-flex align-center">
        <div class="me-auto text-h4">Your information</div>
        <v-btn v-if="disabled" icon="mdi-close-circle-outline text-red-darken-4" variant="flat" @click="handleDeleteClick"></v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <v-card-text>
        <v-form @submit.prevent="handleSaveClick" :disabled="disabled">
            <v-text-field v-model="editedData.name" class="my-2" type="text" label="Name" :error-messages="errors.name"></v-text-field>
            <v-text-field v-model="editedData.surname" class="mb-2" type="text" label="Surname" :error-messages="errors.surname"></v-text-field>
            <v-text-field v-model="editedData.email" class="mb-2" type="email" label="Email" :error-messages="errors.email"></v-text-field>
            <v-text-field v-model="editedData.password" class="mb-2" type="password" label="New password" :error-messages="errors.password"></v-text-field>
            <v-text-field v-model="editedData.passwordConfirmation" class="mb-2" type="password" label="Confirm new password"></v-text-field>
            <div v-if="disabled">
                <v-btn type="button" block class="text-capitalize" color="blue-grey-lighten-5" variant="elevated" @click="handleEnableClick">Edit</v-btn>
            </div>
            <div v-else>
                <v-btn type="submit" block class="text-capitalize" color="blue-grey-lighten-5 mb-2" variant="elevated" :loading="submitting" :disabled="submitting">Save</v-btn>
                <v-btn type="button" block class="text-capitalize" color="blue-grey-lighten-5" variant="elevated" @click="handleCancelClick" :disabled="submitting">Cancel</v-btn>
            </div>
        </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { computed, ref } from 'vue'
export default {
    props: ['data', 'disabled', 'submitting', 'errors'],
    setup(props,context) {
        const editedData = computed({
            get() {
                return props.data
            },
            set(value) {
                context.emit('dataUpdate', value)
            }
        })
        const handleEnableClick = () => {
            context.emit('updateStart')
        }
        const handleCancelClick = () => {
            context.emit('updateCancel')
        }
        const handleSaveClick = () => {
            context.emit('updateConfirm')
        }
        const handleDeleteClick = () => {
            context.emit('delete')
        }
        return { editedData, handleEnableClick, handleCancelClick, handleSaveClick, handleDeleteClick}
    }
}
</script>

<style>

</style>