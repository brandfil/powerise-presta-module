import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';

const firebaseConfig = {
  apiKey: "AIzaSyBEtxpkCxoBIDxLV2s1Ll_Y4-ukDMlrFsw",
  authDomain: "synclines-a425a.firebaseapp.com",
  projectId: "synclines-a425a",
  storageBucket: "synclines-a425a.appspot.com",
  messagingSenderId: "442782026338",
  appId: "1:442782026338:web:0f5af1e52b5840e0fedf3b",
  measurementId: "G-W4JDMHNQPP"
};

const app = initializeApp(firebaseConfig);

const auth = getAuth(app);

export { auth };
