<script setup>
const props = defineProps({ user: Object });
</script>
<template>
  <div class="space-y-5">
    <div class="pb-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
      <h2 class="text-xl font-semibold text-foreground">User Details</h2>
      <p class="text-sm text-muted-foreground mt-1">Complete information about this user</p>
    </div>
    <div class="space-y-4">
      <div>
        <h3 class="font-semibold text-foreground mb-2">{{ user.name }}</h3>
        <p class="text-sm text-muted-foreground">{{ user.email }}</p>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <p class="text-xs text-muted-foreground">Roles</p>
          <p class="text-sm text-foreground">{{ user.roles?.join(', ') || 'N/A' }}</p>
        </div>
        <div>
          <p class="text-xs text-muted-foreground">Status</p>
          <span class="text-sm" :class="user.email_verified_at ? 'text-green-600' : 'text-yellow-600'">
            {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
          </span>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <p class="text-xs text-muted-foreground">Assigned Tasks</p>
          <p class="text-sm text-foreground">{{ user.assigned_tasks_count }}</p>
        </div>
        <div>
          <p class="text-xs text-muted-foreground">Created Tasks</p>
          <p class="text-sm text-foreground">{{ user.created_tasks_count }}</p>
        </div>
      </div>
      <div>
        <p class="text-xs text-muted-foreground">Joined</p>
        <p class="text-sm text-foreground">{{ new Date(user.created_at).toLocaleDateString() }}</p>
      </div>
    </div>
  </div>
</template>
