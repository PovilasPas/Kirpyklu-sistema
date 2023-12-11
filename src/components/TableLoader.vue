<template>
    <v-card color="blue-grey-lighten-5" min-width="350px">
        <v-data-table-virtual loading :headers="headers" density="compact">
            <template v-slot:top>
                <v-toolbar flat color="blue-grey-lighten-5" density="compact">
                    <v-toolbar-title class="mx-4">
                        <v-skeleton-loader type="text"></v-skeleton-loader>
                    </v-toolbar-title>
                </v-toolbar>
            </template>
            <template v-slot:loading>
                <v-skeleton-loader type="table-row@5"></v-skeleton-loader>
            </template>
        </v-data-table-virtual>
    </v-card>
</template>

<script>
export default {
    props: ['headers']
}
</script>

<style>

</style>