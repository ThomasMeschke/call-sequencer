@ECHO OFF

SET BASE_DIR=%~dp0

CMD /C "%BASE_DIR%\..\..\vendor\bin\phpstan analyze -c %BASE_DIR%\phpstan.neon.dist"
