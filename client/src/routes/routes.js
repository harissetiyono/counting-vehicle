import DashboardLayout from "@/pages/Layout/DashboardLayout.vue";

import Dashboard from "@/pages/Dashboard.vue";
import LiveStream from "@/pages/LiveStream.vue";
import LiveDetail from "@/pages/LiveDetail.vue";
import Camera from "@/pages/Camera.vue";
import AddCamera from "@/pages/AddCamera.vue";

const routes = [
  {
    path: "/",
    component: DashboardLayout,
    redirect: "/dashboard",
    children: [
      {
        path: "dashboard",
        name: "Dashboard",
        component: Dashboard
      },
      {
        path: "camera",
        name: "Camera",
        component: Camera
      },
      {
        path: "camera/add",
        name: "Add Camera",
        component: AddCamera
      },
      {
        path: "livestream",
        name: "Live Stream",
        component: LiveStream
      },
      {
        path: "live/:id",
        name: "Live Stream",
        component: LiveDetail
      },
    ]
  }
];

export default routes;
