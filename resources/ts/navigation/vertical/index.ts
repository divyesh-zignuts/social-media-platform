import type { VerticalNavItems } from '@/@layouts/types'

export default [
  {
    title: 'Home',
    to: { name: 'index' },
    icon: { icon: 'tabler-smart-home' },
  },
  {
    title: 'User List',
    to: { name: 'user-list' },
    icon: { icon: 'tabler-users' },
  },
] as VerticalNavItems
