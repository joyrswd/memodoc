
import setModal from './modules/setModal.js';
import errorController from './modules/errorController.js';
import setPartsController from './modules/setPartsController.js';

document.addEventListener("DOMContentLoaded", function() {
    // ツールチップ
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].map(element => new bootstrap.Tooltip(element));
    // エラー強調表示
    errorController('.form-control');
    // モーダル
    setModal('#modal', '.modal-footer .btn-primary', '[data-dialog]');
    // パーツ追加・削除
    setPartsController('[data-parts]', '#parts_badge', 'tbody.table-group-divider>tr');
    //アラート自然消滅
    setTimeout((alert)=>alert?.click(), 3000, document.querySelector('[role="alert"] button'));
});
