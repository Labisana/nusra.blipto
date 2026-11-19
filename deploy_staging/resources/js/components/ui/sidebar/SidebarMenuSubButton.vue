<script setup lang="ts">
import type { PrimitiveProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { Primitive } from "reka-ui"
import { cn } from "@/lib/utils"

const props = withDefaults(defineProps<PrimitiveProps & {
  size?: "sm" | "md"
  isActive?: boolean
  class?: HTMLAttributes["class"]
}>(), {
  as: "a",
  size: "md",
})
</script>

<template>
  <Primitive
    data-slot="sidebar-menu-sub-button"
    data-sidebar="menu-sub-button"
    :as="as"
    :as-child="asChild"
    :data-size="size"
    :data-active="isActive"
    :class="cn(
      'text-sidebar-foreground ring-sidebar-ring hover:bg-[#fb4f00]/8 hover:text-[#fb4f00] dark:hover:bg-[#fb4f00]/15 dark:hover:text-[#ff6b26] active:bg-[#fb4f00]/12 flex h-8 min-w-0 -translate-x-px items-center gap-2 overflow-hidden rounded-md px-2 text-sm transition-colors duration-150 outline-hidden focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-50 aria-disabled:pointer-events-none aria-disabled:opacity-50 [&>span:last-child]:truncate [&>svg]:size-4 [&>svg]:shrink-0',
      'data-[active=true]:bg-[#fb4f00]/12 data-[active=true]:font-medium data-[active=true]:text-[#fb4f00] dark:data-[active=true]:bg-[#fb4f00]/20 dark:data-[active=true]:text-[#ff6b26] data-[active=true]:[&>svg]:text-[#fb4f00] dark:data-[active=true]:[&>svg]:text-[#ff6b26]',
      size === 'sm' && 'text-xs h-7',
      size === 'md' && 'text-sm h-8',
      'group-data-[collapsible=icon]:hidden',
      props.class,
    )"
  >
    <slot />
  </Primitive>
</template>
