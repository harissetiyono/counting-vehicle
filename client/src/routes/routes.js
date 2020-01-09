import DashboardLayout from "@/pages/Layout/DashboardLayout.vue";

import Dashboard from "@/pages/Dashboard.vue";
import LiveStream from "@/pages/LiveStream.vue";
import LiveDetail from "@/pages/LiveDetail.vue";
import Camera from "@/pages/Camera.vue";
import AddCamera from "@/pages/AddCamera.vue";
import Statistics from "@/pages/Statistics.vue";

import FaceDashboard from "@/pages/face_recognition/FaceDashboard.vue";
import FaceCamera from "@/pages/face_recognition/FaceCamera.vue";
import FaceStream from "@/pages/face_recognition/FaceStream.vue";
import FaceStreamDetail from "@/pages/face_recognition/FaceStreamDetail.vue";
import FaceCameraForm from "@/pages/face_recognition/FaceCameraForm.vue";
import PersonData from "@/pages/face_recognition/PersonData.vue";
import PersonForm from "@/pages/face_recognition/PersonForm.vue";
import FaceFind from "@/pages/face_recognition/FaceFind.vue";

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
        path: "camera/:id/edit",
        name: "Edit Camera",
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
      {
        path: "statistics",
        name: "Statistics Report",
        component: Statistics
      },

      // Face Recognition
      {
        path: "face_recognition/dashboard",
        name: "Face Dashboard",
        component: FaceDashboard
      },
      {
        path: "face_recognition/person",
        name: "Person Data",
        component: PersonData
      },
      {
        path: "face_recognition/camera",
        name: "Face Camera Stream",
        component: FaceCamera
      },
      {
        path: "face_recognition/camera/form",
        name: "Add Face Camera",
        component: FaceCameraForm
      },
      {
        path: "face_recognition/camera/:id/edit",
        name: "Edit Face Camera",
        component: FaceCameraForm
      },
      {
        path: "face_recognition/person/form",
        name: "Add New Person",
        component: PersonForm
      },
      {
        path: "face_recognition/person/:id/edit",
        name: "Edit Person data",
        component: PersonForm
      },
      {
        path: "face_recognition/stream",
        name: "Face Stream",
        component: FaceStream
      },
      {
        path: "face_recognition/:id/stream",
        name: "Face Detail Stream",
        component: FaceStreamDetail
      },
      {
        path: "face_recognition/find",
        name: "Face Find",
        component: FaceFind
      },
    ]
  }
];

export default routes;
