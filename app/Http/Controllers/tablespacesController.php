<?php

namespace App\Http\Controllers;
use App\Http\Requests\DumpFileRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class tablespacesController extends Controller
{
    //

    public function index()
    {
        return view('tablespaces.index');
    }

    public static function schemas()
    {
        $data2 = DB::select('select username as schema_name
        from sys.dba_users
        order by username');
    return $data2;


    }
    public function publicPath()
    {
        return response(['path' => public_path() . "\\respaldos"], 200);
    }

    // PARA POST METHOD CREAR TABLESPACE
    public function createtable(Request $request)
    {
      $fields = $request->input('uname');
     
            DB::statement('alter session set "_oracle_script"=true');
            DB::statement("CREATE TABLESPACE " . $fields . " DATAFILE '" . 'C:\\app\\50683\\product\\21c\\oradata\\XE\vscode\\tablespaces\\' . $fields . ".DBF' SIZE 100M AUTOEXTEND ON NEXT 50");
           


      
        return response(['Oracle dice' => 'Tablespace creado con éxito'], 201);
    }
 
 // PARA POST METHOD CREAR TABLESPACE TEMPORAL
    public function createTemporaryTablespace(Request $request)
    {
        $fields = $request->input('uname');

        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("CREATE TEMPORARY TABLESPACE " . $fields . "_TEMP TEMPFILE '" . public_path() . "\\tablespaces\\" . $fields . "_TEMP.DBF' SIZE 25M AUTOEXTEND ON NEXT 50");

        return response(['message' => 'Tablespace creado con éxito'], 201);
    }
    public function deleteTablespace(Request $request)
    {
        $fields = $request->input('uname'); //dee

        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("DROP TABLESPACE " . $fields . " INCLUDING CONTENTS AND DATAFILES");

       // redirect()->route('tablespace.delete')->with('message', 'Tablespace eliminado con éxito');

        return response(['message' => 'Tablespace eliminado con éxito'], 201);
    }

    public static function tablespaces()
    {
        $data =  DB::select(
            'select tablespace_name as tablespace_name
            from dba_tablespaces
            order by tablespace_name'
        );
        return $data;
    
    }
    public function resizeTablespace(Request $request)
    {
        $fields = $request->input('dee');
        $size = $request->input('size');
       

        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("ALTER DATABASE DATAFILE '" . 'C:\\app\\50683\\product\\21c\\oradata\\XE\vscode\\tablespaces\\' . $fields . ".DBF' RESIZE " . $size . "M");

        return response(['message' => 'Tablespace redimensionado con éxito'], 201);
    }

  //METODO POST PARA CAMBIAR EL TAMAÑO DE UN TEMPORARY TABLESPACE
    public function resizeTemporaryTablespace(Request $request)
    {
        $fields = $request->input('dee');
        $size = $request->input('size');;

        //DB::statement("ALTER DATABASE DATAFILE '$resultado' resize " . $fields['size'] . "M");
       // return response(['route' => 'Resize exitoso'], 200);
        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("ALTER DATABASE TEMPFILE '" . public_path() ."\\tablespaces\\" . $fields . ".DBF' RESIZE " . $size . "M");

        return response(['message' => 'Tablespace redimensionado con éxito'], 201);
    }
   
 
    // listar una tabla de una schema(schema = basedatos && tabla = clase)[muestra las columnas de la tabla] 
    /*public static function columnOfATableOfASchema($schema, $table)
    {

        $data = DB::select("select column_name from all_tab_columns where table_name ='" . $table . "' AND OWNER ='" . $schema . "'");
          return $data;      
    }*/
    public function columnOfATableOfASchema(Request $request)
    {
        $schema = $request->input('schemas');
        $table = $request->input('tables');

        $data = DB::select("select column_name from all_tab_columns where table_name ='" . $table . "' AND OWNER ='" . $schema . "'");
         // $data = DB::table('all_tab_columns')->where('table_name', $table)->where('OWNER', $schema)->get();
        return $data;
        


    }

    public function  tablasDeSchemas(Request $request)
    {
  
        $schema = $request->input('uname');
         $data = DB::table('all_tables')
            ->select('table_name')
            ->where('owner', $schema)
            ->orderBy('table_name')
            ->get();
        return $data;
    }

    public function analizeSchema($schema)
    {
      
        DB::statement('alter session set "_oracle_script"=true');

        $tablas = DB::table('all_tables')
            ->select('table_name')
            ->where('owner', $schema)
            ->orderBy('table_name')
            ->get();

        foreach ($tablas as $tabla) {
         DB::statement("ANALYZE TABLE " . $schema . "." . $tabla->table_name . " COMPUTE STATISTICS");
        }

        //return response(['message' => 'Tablas re-analizadas'], 200);
        return response(['message' => 'Tablas analizadas', 'tablas' => $tablas], 200);
       
    }
    //listar privilegios de un usuario 
     public function privileges()
    {
        return DB::table('DBA_SYS_PRIVS')
            ->select('privilege')
            ->distinct()
            ->get();
    }
    //listar roles de un usuario
    public function roles()
    {
        return DB::table('DBA_ROLES')
            ->select('role')
            ->get();
    }
    //asignar un role a un usuario
   /*   METODO POST PARA ASIGNAR UN ROLE A UN USUARIO
    public function assignRole(Request $request)
    {
        $fields = $request->validate([
            "user" => "required",
            "role" => "required",
        ]);

        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("GRANT " . $fields['role'] . " TO " . $fields['user']);

        return response(['message' => 'Rol asignado'], 201);
    }*/
    //Asignar un role a un usuario por metodo get
    public function assignRole($user, $role)
    {
        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("GRANT " . $role . " TO " . $user);
        
        return response(['message' => 'Rol asignado'], 201);
    }
    //listar los usuarios del sistema
    public function users()
    {
        return DB::table('DBA_USERS')
            ->select('username')
            ->get();
    }
    //listar los privilegios de un usuario
    public function privilegesOfAUser($user)
    {
        return DB::table('DBA_SYS_PRIVS')
            ->select('privilege')
            ->where('grantee', $user)
            ->get();
    }
    

    public function createSchemaBackUp(Request $request)
    {
        $fields = $request->input('uname');
        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("CREATE OR REPLACE DIRECTORY RESPALDO AS " . "'" . public_path() . "\\respaldos'");

        // DB::statement('GRANT READ, WRITE ON DIRECTORY RESPALDO TO administrador_fachero');

        $cmd = "EXPDP SYSTEM/coutJonathan97@XE SCHEMAS=" . $fields . " DIRECTORY=RESPALDO DUMPFILE=" . $fields . ".DMP LOGFILE=" . $fields . ".LOG";

        shell_exec($cmd);

       // $path = 'respaldos/' . $schema . '.DMP';

        return response(['message' => 'Respaldado creado con éxito'], 201);
        
    }
    public function borrarRespaldoUsuario(Request $request)
    {
        $schema = $request->input('uname');

        $path = public_path() ."\\respaldos'". $schema . '.DMP';

        File::delete($path);

        $path = public_path() ."\\respaldos'". $schema . '.LOG';

        File::delete($path);

        return response()->json(['message' => 'Respaldo del schema ' . $schema . ' eliminado correctamente'], 200);
    
    }

    // crear un respaldo de una tabla de un esquema

    public function createTableBackUp($schema, $table)
    {
        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("CREATE OR REPLACE DIRECTORY RESPALDO AS " . "'" . public_path() . "\\respaldos'");

        $cmd = "EXPDP SYSTEM/coutJonathan97@XE TABLES=" . $schema . "." . $table . " DIRECTORY=RESPALDO DUMPFILE=" . $table . ".DMP LOGFILE=" . $table . ".LOG";

        shell_exec($cmd);

        $path = 'respaldos/' . $table . '.DMP';

        return response()->json(['message' => 'Respaldo de tabla creado', 'path' => $path], 201);
    }

    public function createDatabaseBackUp(Request $request)
    {
        $database = $request->input('database');
        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("CREATE OR REPLACE DIRECTORY RESPALDO AS " . "'" . public_path() . "\\respaldos'");

        $cmd = "EXPDP SYSTEM/coutJonathan97@XE FULL=Y DIRECTORY=RESPALDO DUMPFILE= " . $database . ".DMP LOGFILE=" . $database . ".LOG";

       try{
        shell_exec($cmd);
        return response()->json(['message' => 'Respaldo de la base de datos ' . $database . ' creado correctamente'], 200);
    } catch (\Throwable $th) {
        return response()->json(['message' => 'Error al crear el respaldo'], 500);
    }
    }
    public function deleteDatabaseBackUp(Request $request)
    {
        $database = $request->input('deletedb');
        $path = public_path() . '\\respaldos\\' . $database . '.DMP';

        File::delete($path);

        $path = public_path() . '\\respaldos\\' . $database . '.LOG';

        File::delete($path);
       
        try {
            File::delete($path);
             return response()->json(['message' => 'Respaldo de la base de datos ' . $database . ' eliminado correctamente'], 200);
            } catch (\Throwable $th) {
                return response()->json(['message' => 'Error al eliminar el respaldo'], 500);
            }
    }
   









    public function createUser(Request $request)
    {
        $fields = $request->validate([
            "uname" => "required",
            "password" => "required",
        ]);

        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("CREATE USER " . $fields['uname'] . " IDENTIFIED BY " . $fields['password']);

        return response(['message' => 'Usuario creado'], 201);
    }
    public function deleteUser(Request $request)
    {
        $fields = $request->validate([
            "uname" => "required",
        ]);

        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("DROP USER " . $fields['uname'] . " CASCADE");

        return response(['message' => 'Usuario eliminado'], 201);
    }
    public function updateUser(Request $request)
    {
        $fields = $request->input('uname');
        $password = $request->input('password');

        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("ALTER USER " . $fields . " IDENTIFIED BY " . $password);

        return response(['message' => 'Usuario actualizado'], 201);
    }
    //AUDITORIA

    public function auditoriaConexiones()
    {
        DB::statement('alter session set "_oracle_script"=true');
        DB::statement('Audit connect');
        return response(['message' => 'Auditoria de conexiones activada'], 201);
    }

    public static function auditoriaGeneral()
    {
        DB::statement('alter session set "_oracle_script"=true');
       
        $data = DB::select('Select distinct userhost as userhost, username as username, action_name as action_name,
         priv_used as priv_used, returncode as returncode from
        dba_audit_trail order by username
        ');
        return $data;


    }
    public static function ejecucionGeneral()
    {
        DB::statement('alter session set "_oracle_script"=true');
        /*return  DB::select('select SUBSTR (LPAD(\'\', LEVEL-1) || OPERATION || \' (\'|| OPTIONS || \')\',1,30 ) as "OPERACION",
             OBJECT_NAME as "OBJETO" FROM  PLAN_TABLE START WITH ID = 0 CONNECT BY PRIOR ID=PARENT_ID
        '); */
       $data = DB::select ('select operation as operation, object_name as object_name from plan_table');
        return $data;
    }
    public static function estado()
    {
        DB::statement('alter session set "_oracle_script"=true');
        $data = DB::select('select status as status, instance_name as instance_name from v$instance');  
        return $data; 
    }
    // analizar una tabla de un schema
    public function analizeTableOfSchema(Request $request)
    {
        $schema = $request->input('schemas');
        $table = $request->input('tables');

        DB::statement('alter session set "_oracle_script"=true');

        DB::statement("ANALYZE TABLE " . $schema . "." . $table . " COMPUTE STATISTICS");

        return response(['message' => 'Tablas re-analizadas'], 200);
    }


}
