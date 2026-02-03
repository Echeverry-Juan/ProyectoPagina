import React, { useState } from 'react';
import axios from 'axios';
import Formulario from '../../components/FormularioComponent';
import { useNavigate, Link } from 'react-router-dom';

const url = process.env.REACT_APP_BACK_URL;

const Register = () => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const navigate = useNavigate();

  const handleRegister = ({ nombre_usuario, clave }) => {
    setLoading(true);
    setError('');
    setSuccess('');

    axios
      .post(`${url}/register`, { nombre_usuario, clave })
      .then((response) => {
        if (response.data.exists) {
          setError('El nombre de usuario ya está en uso.');
        } else {
          setSuccess('Registro exitoso. Puedes iniciar sesión.');
        }
      })
      .catch((error) => {
        //cambios
        setError(error.response?.data || "Error al registrarse");
      })
      .finally(() => setLoading(false));
  };

  const handleGoToInicio = () => {
    navigate('/');
  };

  const handleReset = () => {
    setError('');
    setSuccess('');
  };

  return (
    <section className='formularioPage'>
      <h2 className='tituloPage'>Registro</h2>
      <Formulario onData={handleRegister} loading={loading} onReset={handleReset} />
      {error && <p style={{ color: 'red' }}>{error}</p>}
      {success && <p>{success} <Link to='/login'>Inicia Sesion</Link></p>}
      <p>¿Ya tienes cuenta? <Link to='/login'>Iniciar Sesion</Link></p>
      <button onClick={handleGoToInicio}>Ir a inicio</button>
    </section>
  );
};

export default Register;
