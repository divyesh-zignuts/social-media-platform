import type { VerticalNavItems } from '@/@layouts/types'

export default [
  {
    title: 'Home',
    to: { name: 'index' },
    icon: { icon: 'tabler-smart-home' },
    accessBy: ['admin', 'user'],
  },
  {
    title: 'Users',
    to: { name: 'user-list' },
    icon: { icon: 'tabler-users' },
    accessBy: ['admin'],
  },
  {
    title: 'Posts',
    to: { name: 'post-list' },
    icon: { icon: 'tabler-wallpaper' },
    accessBy: ['admin','user'],
  },
] as unknown as  VerticalNavItems
