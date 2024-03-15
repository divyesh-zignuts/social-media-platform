import type { VerticalNavItems } from '@/@layouts/types'

export default [
  {
    title: 'Home',
    to: { name: 'index' },
    icon: { icon: 'tabler-smart-home' },
  },
  {
    title: 'Users',
    to: { name: 'user-list' },
    icon: { icon: 'tabler-users' },
  },
  {
    title: 'Posts',
    to: { name: 'post-list' },
    icon: { icon: 'tabler-wallpaper' },
  },
] as VerticalNavItems
