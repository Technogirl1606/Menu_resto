<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    items: { type: Array, required: true },
    categories: { type: Array, required: true },
});

const editingId = ref(null); // null = mode "ajout", sinon id du plat en cours de modification
const imagePreview = ref(null);

const form = useForm({
    category_id: props.categories[0]?.id ?? '',
    name: '',
    description: '',
    price: '',
    is_available: true,
    image: null,
});

function onImageChange(e) {
    const file = e.target.files[0];
    form.image = file ?? null;
    imagePreview.value = file ? URL.createObjectURL(file) : null;
}

function startEdit(item) {
    editingId.value = item.id;
    form.category_id = item.category_id;
    form.name = item.name;
    form.description = item.description ?? '';
    form.price = item.price;
    form.is_available = item.is_available;
    form.image = null;
    imagePreview.value = item.image_url;
}

function cancelEdit() {
    editingId.value = null;
    form.reset();
    imagePreview.value = null;
}

function submit() {
    if (editingId.value) {
        form.post(`/admin/items/${editingId.value}`, {
            forceFormData: true,
            onSuccess: () => cancelEdit(),
        });
    } else {
        form.post('/admin/items', {
            forceFormData: true,
            onSuccess: () => cancelEdit(),
        });
    }
}

function destroy(item) {
    if (confirm(`Supprimer "${item.name}" ?`)) {
        router.delete(`/admin/items/${item.id}`);
    }
}
</script>

<template>
    <div class="max-w-3xl mx-auto py-8 px-4">
        <h1 class="text-xl font-medium mb-6">Gestion des plats</h1>

        <!-- Formulaire ajout / modification -->
        <form @submit.prevent="submit" class="border border-gray-200 rounded-xl p-4 mb-8 space-y-3">
            <p class="font-medium text-sm">{{ editingId ? 'Modifier le plat' : 'Ajouter un plat' }}</p>

            <select v-model="form.category_id" class="w-full border rounded px-3 py-2 text-sm">
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>

            <input v-model="form.name" type="text" placeholder="Nom du plat" class="w-full border rounded px-3 py-2 text-sm" />
            <textarea v-model="form.description" placeholder="Description" class="w-full border rounded px-3 py-2 text-sm"></textarea>
            <input v-model="form.price" type="number" step="0.01" placeholder="Prix (F CFA)" class="w-full border rounded px-3 py-2 text-sm" />

            <div>
                <label class="block text-sm text-gray-600 mb-1.5">Photo du plat</label>
                <div class="flex items-center gap-3">
                    <div class="w-20 h-20 shrink-0 rounded-lg border border-gray-200 bg-red-50 overflow-hidden flex items-center justify-center">
                        <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                        <span v-else class="text-red-300 text-xs">Aperçu</span>
                    </div>
                    <label class="cursor-pointer text-sm border border-gray-300 rounded px-3 py-2 hover:bg-gray-50">
                        Choisir une photo
                        <input type="file" accept="image/*" @change="onImageChange" class="hidden" />
                    </label>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.is_available" type="checkbox" />
                Disponible
            </label>

            <div class="flex gap-2 pt-2">
                <button type="submit" :disabled="form.processing" class="bg-red-600 text-white text-sm px-4 py-2 rounded">
                    {{ editingId ? 'Enregistrer' : 'Ajouter' }}
                </button>
                <button v-if="editingId" type="button" @click="cancelEdit" class="text-sm px-4 py-2 rounded border">
                    Annuler
                </button>
            </div>

            <p v-if="form.errors.name" class="text-red-600 text-xs">{{ form.errors.name }}</p>
            <p v-if="form.errors.image" class="text-red-600 text-xs">{{ form.errors.image }}</p>
        </form>

        <!-- Liste des plats existants -->
        <div class="space-y-2">
            <div v-for="item in items" :key="item.id" class="flex items-center gap-3 border border-gray-100 rounded-lg p-2">
                <img v-if="item.image_url" :src="item.image_url" class="w-12 h-12 object-cover rounded" />
                <div v-else class="w-12 h-12 bg-red-50 rounded"></div>

                <div class="flex-1">
                    <p class="text-sm font-medium">{{ item.name }}</p>
                    <p class="text-xs text-gray-500">{{ item.category?.name }} · {{ item.price }} F</p>
                </div>

                <button @click="startEdit(item)" class="text-xs px-2 py-1 border rounded">Modifier</button>
                <button @click="destroy(item)" class="text-xs px-2 py-1 border rounded text-red-600">Supprimer</button>
            </div>
        </div>
    </div>
</template>