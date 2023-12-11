<template>
  <v-dialog v-model="show" min-width="335px" width="500px" persistent :scrim="false">
    <v-card class="text-center">
        <v-card-text>
            <div class="text-grey-darken3 text-h5">Loading...</div>
            <v-progress-linear indeterminate color="blue-gry-darken-3"></v-progress-linear>
        </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import { computed, ref, watch } from 'vue'
export default {
    props: ['modelValue'],
    setup(props, context){
        const show = computed(() => {
            return props.modelValue
        }, (value) => {
            context.emit('input', value)
        })
        return { show }
    }
}
</script>

<style>

</style>