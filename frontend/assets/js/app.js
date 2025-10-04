const API = 'http://localhost:8000';
let token = null, me = null;

// helper para querySelector
function qs(selector) {
  return document.querySelector(selector);
}

// REGISTER
qs('#registerForm').addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const body = { email: fd.get('email'), name: fd.get('name'), password: fd.get('password') };

  try {
    const res = await fetch(API + '/users', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    });
    const data = await res.json();
    qs('#registerResult').textContent = JSON.stringify(data, null, 2);
  } catch (err) {
    qs('#registerResult').textContent = 'Error: ' + err;
  }
});

// LOGIN
qs('#loginForm').addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const body = { email: fd.get('email'), password: fd.get('password') };

  try {
    const res = await fetch(API + '/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    });
    const data = await res.json();
    qs('#loginResult').textContent = JSON.stringify(data, null, 2);

    if (res.ok) {
      token = data.token;
      me = data;
      qs('#profile').textContent = `Hello ${data.name} (id ${data.iduser}) - drinks: ${data.drinkCounter}`;
    }
  } catch (err) {
    qs('#loginResult').textContent = 'Error: ' + err;
  }
});

// LIST USERS PUBLIC (daily drinks)
qs('#listUsers').addEventListener('click', async () => {
  try {
    const resUsers = await fetch(API + '/public/users/history');
    const users = await resUsers.json();
    qs('#usersList').textContent = JSON.stringify(users, null, 2);
  } catch (err) {
    qs('#usersList').textContent = 'Error: ' + err;
  }
});

// INCREMENT DRINK
qs('#incDrink').addEventListener('click', async () => {
  if (!me) return alert('Please login first');
  try {
    const res = await fetch(API + `/users/${me.iduser}/drink`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
      body: JSON.stringify({ drink: 1 })
    });
    const data = await res.json();
    me.drinkCounter = data.drinkCounter;
    qs('#profile').textContent = `Hello ${me.name} (id ${me.iduser}) - drinks: ${me.drinkCounter}`;
  } catch (err) {
    console.error(err);
    alert('Error incrementing drink');
  }
});

// REFRESH PROFILE
qs('#refreshProfile').addEventListener('click', async () => {
  if (!me) return alert('Please login first');
  try {
    const res = await fetch(API + `/users/${me.iduser}`, {
      headers: { 'Authorization': 'Bearer ' + token }
    });
    const data = await res.json();
    me = data;
    qs('#profile').textContent = `Hello ${me.name} (id ${me.iduser}) - drinks: ${me.drinkCounter}`;
  } catch (err) {
    console.error(err);
    alert('Error refreshing profile');
  }
});

// EDIT USER
qs('#editUserForm').addEventListener('submit', async e => {
  e.preventDefault();
  if (!me) return alert('Please login first');

  const fd = new FormData(e.target);
  const body = {};
  if (fd.get('name')) body.name = fd.get('name');
  if (fd.get('password')) body.password = fd.get('password');

  try {
    const res = await fetch(API + `/users/${me.iduser}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
      body: JSON.stringify(body)
    });
    const data = await res.json();
    qs('#editResult').textContent = JSON.stringify(data, null, 2);
    if (res.ok) me = data;
  } catch (err) {
    qs('#editResult').textContent = 'Error: ' + err;
  }
});

// DELETE USER
qs('#deleteUser').addEventListener('click', async () => {
  if (!me) return alert('Please login first');
  if (!confirm('Are you sure you want to delete your account?')) return;

  try {
    const res = await fetch(API + `/users/${me.iduser}`, {
      method: 'DELETE',
      headers: { 'Authorization': 'Bearer ' + token }
    });
    const data = await res.json();
    qs('#deleteResult').textContent = JSON.stringify(data, null, 2);
    if (res.ok) {
      alert('Account deleted!');
      token = null;
      me = null;
      qs('#profile').textContent = '';
    }
  } catch (err) {
    qs('#deleteResult').textContent = 'Error: ' + err;
  }
});
