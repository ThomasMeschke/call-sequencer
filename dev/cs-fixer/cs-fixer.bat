@ECHO OFF

SET BASE_DIR=%~dp0

CMD /C "%BASE_DIR%\..\..\vendor\bin\php-cs-fixer fix %BASE_DIR%\..\..\src --config=%BASE_DIR%\.php-cs-fixer.php"
