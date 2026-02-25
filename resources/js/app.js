import "./bootstrap";
import TomSelect from "tom-select";
window.TomSelect = TomSelect;
// FilePond
import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
// FilePond CSS
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
// FilePond register
FilePond.registerPlugin(FilePondPluginImagePreview);
window.FilePond = FilePond;