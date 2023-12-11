import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import RegisterView from '../views/RegisterView.vue'
import LoginView from '../views/LoginView.vue'
import UserInfoView from '../views/UserInfoView'
import HairSalonListView from '../views/HairSalonListView.vue'
import HairdresserListView from '../views/HairdresserListView.vue'
import HairstyleListView from '../views/HairstyleListView.vue'
import NotFoundView from '../views/NotFoundView.vue'
import { jwtDecode } from 'jwt-decode'
import { POSITION, useToast } from 'vue-toastification'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: {
      checkAuth: false
    }
  },
  {
    path: '/users/register',
    name: 'register',
    component: RegisterView,
    meta: {
      checkAuth: true,
      auth: false
    }
  },
  {
    path: '/users/login',
    name: 'login',
    component: LoginView,
    meta: {
      checkAuth: true,
      auth: false
    }
  },
  {
    path: '/users/:userId',
    name: 'user',
    component: UserInfoView,
    props: true,
    meta: {
      checkAuth: true,
      auth: true
    }
  },
  {
    path: '/cities/:cityId/hair-salons',
    name: 'hairSalonList',
    component: HairSalonListView,
    props: true,
    meta: {
      checkAuth: false
    }
  },
  {
    path: '/cities/:cityId/hair-salons/:salonId/hairdressers',
    name: 'hairdresserList',
    component: HairdresserListView,
    props: true,
    meta: {
      checkAuth: false
    }
  },
  {
    path: '/cities/:cityId/hair-salons/:salonId/hairdressers/:hairdresserId/hairstyles',
    name: 'hairstyleList',
    component: HairstyleListView,
    props: true,
    meta: {
      checkAuth: false
    }
  },
  {
    path: '/managers/:managerId/hair-salons',
    name: 'managerHairSalonList',
    component: HairSalonListView,
    props: true,
    meta: {
      checkAuth: true,
      auth: true
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'notFound',
    component: NotFoundView
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach((to, from) => {
  if(to.meta.checkAuth){
    const token = localStorage.getItem('token')
    if(to.meta.auth && !token) return  { name: 'login' }
    else if(!to.meta.auth && token) return { name: 'home' }
  }
})

export default router
