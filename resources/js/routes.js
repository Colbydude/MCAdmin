import Dashboard from './views/Dashboard';
import NotFound from './views/NotFound';

export default {
    mode: 'history',

    linkActiveClass: 'font-bold',

    routes: [
        {
            path: '/admin/*',
            component: NotFound
        },

        {
            path: '/admin',
            component: Dashboard
        },
    ],
};
