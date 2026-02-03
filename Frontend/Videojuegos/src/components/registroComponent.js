import { Link } from "react-router-dom";


export default function Registro(){
    
    const tiempotoken = localStorage.getItem("token-time");
    const token = localStorage.getItem("token");
    const tiempoact = new Date().getTime();

    if (!token || !tiempotoken || tiempotoken < tiempoact){
    return(
        <div>
            <p>¿No esta registrado?</p>
            <button ><Link to='/register'>Registrarse</Link></button>
        </div>
    )}else{
        <>
        </>
    }

}