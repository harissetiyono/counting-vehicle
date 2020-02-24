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

import ANPRStream from "@/pages/anpr/ANPRStream.vue";
import ANPRStreamDetail from "@/pages/anpr/ANPRStreamDetail.vue";
import ANPRCamera from "@/pages/anpr/ANPRCamera.vue";
import ANPRCameraForm from "@/pages/anpr/ANPRCameraForm.vue";
import ANPRData from "@/pages/anpr/ANPRData.vue";
import ANPRFiltering from "@/pages/anpr/ANPRFiltering.vue";
import ANPRViolation from "@/pages/anpr/ANPRViolation.vue";

const routes = [
  {
    path: "/",
    component: DashboardLayout,
    redirect: "/dashboard",
    children: [
      {
        path: "dashboard",
        name: "Dashboard",
        component: Dashboard,
        meta: {
          auth: true,
          title: 'Counting Dashboard'
        }
      },
      {
        path: "camera",
        name: "Camera",
        component: Camera,
        meta: {
          auth: true,
          title: 'Counting Cameras'
        }
      },
      {
        path: "camera/add",
        name: "Add Camera",
        component: AddCamera,
        meta: {
          auth: true,
          title: 'Add Camera Counting'
        }
      },
      {
        path: "camera/:id/edit",
        name: "Edit Camera",
        component: AddCamera,
        meta: {
          auth: true,
          title: 'Edit Camera Counting'
        }
      },
      {
        path: "livestream",
        name: "Live Stream",
        component: LiveStream,
        meta: {
          auth: true,
          title: 'Counting Live Stream'
        }
      },
      {
        path: ":id/live",
        name: "Live Stream Detail",
        component: LiveDetail,
        meta: {
          auth: true,
          title: 'Counting Detail'
        }
      },
      {
        path: "statistics",
        name: "Statistics Report",
        component: Statistics,
        meta: {
          auth: true,
          title: 'Counting Statistics'
        }
      },

      // Face Recognition
      {
        path: "face_recognition/dashboard",
        name: "Face Dashboard",
        component: FaceDashboard,
        meta: {
          auth: true,
          title: 'Face Recognition Dashboard'
        }
      },
      {
        path: "face_recognition/person",
        name: "Person Data",
        component: PersonData,
        meta: {
          auth: true,
          title: 'Add Person Data'
        }
      },
      {
        path: "face_recognition/camera",
        name: "Face Camera Stream",
        component: FaceCamera,
        meta: {
        auth: true,
        title: 'Face Recognition Camera'
      }
      },
      {
        path: "face_recognition/camera/form",
        name: "Add Face Camera",
        component: FaceCameraForm,
        meta: {
        auth: true,
        title: 'Add Face Recognition Camera'
      }
      },
      {
        path: "face_recognition/camera/:id/edit",
        name: "Edit Face Camera",
        component: FaceCameraForm,
        meta: {
        auth: true,
        title: 'Edit Face Recognition Camera'
      }
      },
      {
        path: "face_recognition/person/form",
        name: "Add New Person",
        component: PersonForm,
        meta: {
        auth: true,
        title: 'Add Person'
      }
      },
      {
        path: "face_recognition/person/:id/edit",
        name: "Edit Person data",
        component: PersonForm,
        meta: {
        auth: true,
        title: 'Edit Person'
      }
      },
      {
        path: "face_recognition/stream",
        name: "Face Stream",
        component: FaceStream,
        meta: {
        auth: true,
        title: 'Face Recognition Live Stream'
      }
      },
      {
        path: "face_recognition/:id/stream",
        name: "Face Detail Stream",
        component: FaceStreamDetail,
        meta: {
        auth: true,
        title: 'Face Recognition Stream Detail'
      }
      },
      {
        path: "face_recognition/find",
        name: "Face Find",
        component: FaceFind,
        meta: {
        auth: true,
        title: 'Find Face '
      }
      },

      // ANPR Routes
      {
        path: "anpr/stream",
        name: "ANPR Stream",
        component: ANPRStream,
        meta: {
          auth: true,
          title: 'ANPR Live Stream'
        }
      },
      {
        path: "anpr/data",
        name: "ANPR Data",
        component: ANPRData,
        meta: {
          auth: true,
          title: 'ANPR Data Record'
        }
      },
      {
        path: "anpr/:id/stream",
        name: "ANPR Stream Detail",
        component: ANPRStreamDetail,
        meta: {
          auth: true,
          title: 'ANPR Stream Detail'
        }
      },
      {
        path: "anpr/camera",
        name: "ANPR Camera",
        component: ANPRCamera,
        meta: {
          auth: true,
          title: 'ANPR Camera'
        }
      },
      {
        path: "anpr/camera/add",
        name: "ANPR Add Camera",
        component: ANPRCameraForm,
        meta: {
          auth: true,
          title: 'ANPR Add Camera'
        }
      },
      {
        path: "anpr/:id/edit",
        name: "ANPR Edit Camera",
        component: ANPRCameraForm,
        meta: {
          auth: true,
          title: 'ANPR Edit Camera'
        }
      },
      {
        path: "anpr/filtering",
        name: "ANPR Filtering",
        component: ANPRFiltering,
        meta: {
          auth: true,
          title: 'ANPR Filtering'
        }
      },
      {
        path: "anpr/violation",
        name: "ANPR Violation",
        component: ANPRViolation,
        meta: {
          auth: true,
          title: 'ANPR Violation'
        }
      },
    ]
  }
];

export default routes;
