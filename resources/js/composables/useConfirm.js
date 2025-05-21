import Swal from "sweetalert2";

export function useConfirm() {
  const confirm = async (title = "Are you sure?", html = "", icon = "question") => {
    const result = await Swal.fire({
      title,
      html,
      icon,
      showCancelButton: true,
      confirmButtonText: "はい",
      cancelButtonText: "いいえ",
      customClass: {
        popup: 'rounded-2xl shadow-xl border border-neutral-100',
        title: 'text-lg font-bold text-neutral-800',
        htmlContainer: 'text-neutral-600 text-base',
        actions: 'gap-3',
        confirmButton: 'bg-rose-500 hover:bg-rose-600 text-white font-medium px-6 py-2.5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200',
        cancelButton: 'bg-white hover:bg-neutral-50 text-neutral-600 font-medium px-6 py-2.5 rounded-xl border border-neutral-200 shadow-sm hover:shadow-md transition-all duration-200',
        closeButton: 'text-neutral-400 hover:text-neutral-600 transition-colors duration-200',
        icon: 'border-4 border-neutral-100',
      },
      buttonsStyling: false,
      showCloseButton: true,
      reverseButtons: true,
      padding: '1.5rem',
      background: '#ffffff',
      backdrop: 'rgba(0, 0, 0, 0.4)',
      allowOutsideClick: true,
      allowEscapeKey: true,
      focusConfirm: false,
      focusCancel: true,
    });

    return result.isConfirmed;
  };

  const alert = async (title = "Alert", html = "", icon = "info", onClose = () => {}) => {
    await Swal.fire({
      title,
      html,
      icon,
      confirmButtonText: "OK",
      customClass: {
        popup: 'rounded-2xl shadow-xl border border-neutral-100',
        title: 'text-lg font-bold text-neutral-800',
        htmlContainer: 'text-neutral-600',
        actions: 'gap-3',
        confirmButton: 'bg-sky-500 hover:bg-sky-600 text-white font-medium px-6 py-2.5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200',
        closeButton: 'text-neutral-400 hover:text-neutral-600 transition-colors duration-200',
        icon: 'border-4 border-neutral-100',
      },
      buttonsStyling: false,
      showCloseButton: true,
      padding: '1.5rem',
      background: '#ffffff',
      backdrop: 'rgba(0, 0, 0, 0.4)',
      allowOutsideClick: true,
      allowEscapeKey: true,
      focusConfirm: false,
    }).then(() => {
      onClose();
    });
  };

  return { confirm, alert };
}
