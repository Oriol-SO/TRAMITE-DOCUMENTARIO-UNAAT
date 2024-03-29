function page(path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [
  { path: '/', redirect: { name: 'login' } },

  { path: '/login', name: 'login', component: page('auth/login.vue') },

  { path: '/home', name: 'home', component: page('home.vue') },

  //{ path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },

  {
    path: '/settings',
    component: page('settings/index.vue'),
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
    ]
  },
  {
    path: '/admin',
    component: page('admin/index.vue'),
    children: [
      { path: '', redirect: { name: 'dash.admin' } },
      { path: 'dashboard', name: 'dash.admin', component: page('admin/dashboard.vue') },
      { path: 'reportes', name: 'admin.repo', component: page('admin/reportes.vue') },
      { path: 'oficinas', name: 'admin.oficinas', component: page('admin/oficinas.vue') },
      { path: 'archivos', name: 'admin.archivos', component: page('admin/archivos.vue') },
    ]
  },
  {
    path: '/mesa-de-partes',
    component: page('meza/index.vue'),
    children: [
      { path: '', redirect: { name: 'dash.meza' } },
      { path: 'dashboard', name: 'dash.meza', component: page('meza/dashboard.vue') },
      { path: 'documento/:id', name: 'meza.doc', component: page('meza/documento.vue') },
    ]
  },
  {
    path: '/unidad-organica',
    component: page('unidad/index.vue'),
    children: [
      { path: '', redirect: { name: 'dash.unidad' } },
      { path: 'dashboard', name: 'dash.unidad', component: page('unidad/dashboard.vue') },
      { path: 'documento/:id', name: 'unidad.doc', component: page('unidad/documento.vue') },
    ]
  },

  { path: '*', component: page('errors/404.vue') }
]
