
import setModal from './modules/setModal.js';
import emphasisError from './modules/emphasisError.js';
import setPartsController from './modules/setPartsController.js';

document.addEventListener("DOMContentLoaded", function() {
    // ツールチップ
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].map(element => new bootstrap.Tooltip(element));
    // エラー強調表示
    emphasisError('.form-control');
    // モーダル
    setModal('#modal', '.modal-footer .btn-primary', '[data-dialog]');
    // パーツ追加・削除
    setPartsController('[data-parts]', '#parts_badge', 'tbody.table-group-divider>tr');
});
