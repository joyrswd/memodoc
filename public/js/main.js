
import modalController from './modules/modalController.js';
import errorController from './modules/errorController.js';
import partsController from './modules/partsController.js';
import tagController from './modules/tagController.js';

document.addEventListener("DOMContentLoaded", function() {
    // ツールチップ
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].map(element => new bootstrap.Tooltip(element));
    // エラー強調表示
    errorController('.form-control');
    // モーダル
    modalController('#modal', '.modal-footer .btn-primary', '[data-dialog]');
    // パーツ追加・削除
    partsController('[data-parts]', '#parts_badge', 'tbody.table-group-divider>tr');
    // タグ操作追加
    tagController(document.querySelector('[data-x="post"]'));
    //アラート自然消滅
    setTimeout((alert)=>alert?.click(), 3000, document.querySelector('[role="alert"] button'));
});
