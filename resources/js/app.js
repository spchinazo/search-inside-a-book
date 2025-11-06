import '../css/app.css';
import { createApp } from 'vue';

import { DocumentViewer } from './features';

const app = createApp({});

app.component('document-viewer', DocumentViewer);

app.mount('#app');
