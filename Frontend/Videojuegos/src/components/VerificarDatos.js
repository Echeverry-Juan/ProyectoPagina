

    const regexclave= /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/;
    const regexUsuario= /^[a-zA-Z0-9]{6,20}$/;
    function verificarNombre(nombre_usuario){
        return (regexUsuario.test(nombre_usuario))};
     function verificarClave(clave){
        return(regexclave.test(clave))};
        



export  {verificarNombre , verificarClave};