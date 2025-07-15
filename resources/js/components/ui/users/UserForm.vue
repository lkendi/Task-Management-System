<script setup>
import { ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
const props = defineProps({ user: Object, roles: Array });
const emit = defineEmits(['submitted']);

const form = useForm({
  name: props.user?.name || '',
  email: props.user?.email || '',
  password: '',
  password_confirmation: '',
  role: props.user?.roles?.[0] || (props.roles?.[0] || ''),
});

function submit() {
  if (props.user) {
    form.put(`/users/${props.user.id}`, {
      onSuccess: () => emit('submitted'),
    });
  } else {
    form.post('/users', {
      onSuccess: () => emit('submitted'),
    });
  }
}
</script>
<template>
  <form @submit.prevent="submit" class="space-y-5">
    <h2 class="text-xl font-semibold text-foreground pb-2 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
      {{ props.user ? 'Edit User' : 'Create User' }}
    </h2>
    <div class="space-y-3">
      <div>
        <label class="block text-sm font-medium text-foreground mb-1.5">Name</label>
        <input v-model="form.name" placeholder="Name" required class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]" />
        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-foreground mb-1.5">Email</label>
        <input v-model="form.email" placeholder="Email" required class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]" />
        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-foreground mb-1.5">Password</label>
        <input v-model="form.password" :required="!props.user" type="password" placeholder="Password" class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]" />
        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-foreground mb-1.5">Confirm Password</label>
        <input v-model="form.password_confirmation" :required="!props.user" type="password" placeholder="Confirm Password" class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]" />
        <p v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ form.errors.password_confirmation }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-foreground mb-1.5">Role</label>
        <select v-model="form.role" class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]">
          <option v-for="role in props.roles" :key="role" :value="role">{{ role }}</option>
        </select>
        <p v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</p>
      </div>
    </div>
    <div class="pt-3 flex justify-end gap-3">
      <button type="button" class="px-4 py-2 text-sm rounded-md border border-[#e3e3e0] text-foreground hover:bg-[#f5f5f4] dark:hover:bg-[#2d2d2b] dark:border-[#3E3E3A]" @click="$emit('submitted')">
        Cancel
      </button>
      <button type="submit" class="px-4 py-2 text-sm rounded-md bg-[#1b1b18] text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1C1C1A] dark:hover:bg-white" :disabled="form.processing">
        Save User
      </button>
    </div>
  </form>
</template>
