<?
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404", "Y");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Страница не найдена");
?>

	<div class="block-404 no-padding-xs-only">
		<div class="container">
			<div class="block-404__plate">
				<div class="block-404__plate__title">404</div>
				<div class="block-404__plate__text">Страница не найдена, Вы можете
					<a href="/">вернуться на главную страницу</a>
					.
				</div>
			</div>
		</div>
	</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>