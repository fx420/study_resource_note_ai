document.addEventListener('DOMContentLoaded', () => {
    // Data arrays
    const universities = [
      'Universiti Malaya',
      'Universiti Kebangsaan Malaysia',
      'Universiti Putra Malaysia',
      'Universiti Teknologi Malaysia',
      'Monash University Malaysia',
      "Taylor's University",
      'Sunway University'
    ];
    const courses = [
      'Software Engineering',
      'Mechanical Engineering',
      'Business',
      'Accountancy'
    ];
  
    // Fill University & Course selects
    const uniSel = document.getElementById('universityInput');
    const courseSel = document.getElementById('courseInput');
    const currentUni = document.getElementById('universityDisplay').textContent.trim();
    const currentCourse = document.getElementById('courseDisplay').textContent.trim();
    universities.forEach(u => {
      let opt = new Option(u,u);
      if (u === currentUni) opt.selected = true;
      uniSel.add(opt);
    });
    courses.forEach(c => {
      let opt = new Option(c,c);
      if (c === currentCourse) opt.selected = true;
      courseSel.add(opt);
    });
  
    const genderButtons = document.querySelectorAll('#genderInput button');
    const genderValue = document.getElementById('genderValue');

    genderButtons.forEach(btn => {
      if (btn.dataset.value === genderValue.value) btn.classList.add('active');
      btn.addEventListener('click', () => {
        genderButtons.forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        genderValue.value = btn.dataset.value;
      });
    });
  
    const pwdToggle = document.querySelector('.toggle-password');
    const pwdInput = document.getElementById('passwordInput');
    pwdToggle.addEventListener('click', () => {
      const type = pwdInput.type === 'password' ? 'text' : 'password';
      pwdInput.type = type;
      pwdToggle.innerHTML = type==='password'
        ? '<i class="fas fa-eye"></i>'
        : '<i class="fas fa-eye-slash"></i>';
    });
  
    const editBtn    = document.getElementById('editBtn');
    const saveCancel = document.getElementById('saveCancelBtns');
    const form       = document.getElementById('profileForm');
    const fields     = [
      'username','email','gender','dob',
      'university','course','password'
    ];
  
    editBtn.addEventListener('click', () => toggleEdit(true));
    window.cancelEdit = () => {
      form.classList.remove('was-validated');
      toggleEdit(false);
    };
  
    function toggleEdit(on) {
      fields.forEach(f => {
        document.getElementById(f+'Display').classList.toggle('d-none', on);
        document.getElementById(f + (f==='gender'?'Input':'Input')).classList.toggle('d-none', !on);
      });
      document.querySelector('.password-group').classList.toggle('d-none', !on);
      editBtn.classList.toggle('d-none', on);
      saveCancel.classList.toggle('d-none', !on);
    }
  
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        form.classList.add('was-validated');
      }
    });
  });
  