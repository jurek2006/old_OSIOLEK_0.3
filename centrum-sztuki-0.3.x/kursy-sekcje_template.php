<!--Template wczytywana do szablonu kursy-sekcje.php - używana na dwa sposoby - gdy otwarta jest lista kursów albo pojedynczy kursy - wyświetla tabelkę szczegóły-->

								<tr><td colspan="4">
                            	<h1 class="entry-title">
                                     <a href="<?php echo esc_url( $permalink); ?>" rel="bookmark"><?php _e( $title , 'PP2014' ); ?></a>
                                </h1>
                                </td>
                                </tr>
                                
								<?php 
                                //jeśli użytkownik jest zalogowany wyświetlenie wartości "kolejność na stronie" - wg którego sortowane są wpisy
                                if ( is_user_logged_in() ) {
                                     echo '<tr><td colspan="4"><p class="kursy_kolejnosc_na_stronie">Kolejność na stronie: '.$pods->display('kolejnosc_na_stronie').'</p></td>
                                </tr>';
                                } ?>
                                

								<tr>
                                	<td colspan="2">Prowadzący: <?php echo $prowadzacy ?></td>
                                    <td colspan="2"><?php echo $lokalizacje ?></td>
                                </tr>
                                
                                <tr>
                                	<th>Grupa</th>
                                    <th>Opłata</th>
                                    <th>Dni tygodnia</th>
                                    <th>Godziny zajęć</th>
                                </tr>
                         
                                
                                
                                
                                
                                
                            	<?php 
									//pobieranie nazwy i pola opłaty grupy kursowej
									$grupa_kursowa_name = $pods->field('grupa_kursowa.name');
									$grupa_kursowa_oplata = $pods->field('grupa_kursowa.oplata');						
									
									
									for($i=0; $i<7; $i++)
									//pobieranie tabel dni tygodnia i godzin dla danych grup kursowych
									{
										$nazwa = 'grupa_kursowa.dzien_tygodnia_'.($i+1);
										$grupa_kursowa_dzien_tygodnia[$i] = $pods->field($nazwa);
										
										$nazwa = 'grupa_kursowa.godziny_dnia_'.($i+1);
										$grupa_kursowa_godziny_dnia[$i] = $pods->field($nazwa);
			
									}
									//złożenie tabel $grupa_kursowa_dzien_tygodnia i $grupa_kursowa_godziny_dnia w jedną tabelę $kursy[
									//w tabeli $kursy pierwszy parametr określa czy pobieramy wartość dnia tygodnia, czy godziny dla niego
									//drugi parametr określa który z kolei dzień tygodnia i godzinę pobieramy dla danej grupy kursowej
									//trzeci parametr to numer grupy kursowej
									$kursy['dzien_tygodnia'] = $grupa_kursowa_dzien_tygodnia;
									$kursy['godziny_dnia'] = $grupa_kursowa_godziny_dnia;
									
									for($j=0; $j < count($grupa_kursowa_name); $j++){
									//pętla po wszystkich grupach kursowych danego kursu - $j określa kolejną grupę
										//$output - wartość linii/wiersza w tabeli dla danego dnia danej grupy kursowej
										$output = '';
										//$licznik - zlicza, który jest to wiersz dla danej grupy kursowej
										//umożliwia to grupowanie nazwy grupy kursowej i opłaty za pomocą rowspan
										$licznik = 0;
										for($i=0; $i<7; $i++){
										//pętla po kolejnych dniach tygodnia danej grupy kursowej
											if(!empty($kursy['dzien_tygodnia'][$i][$j])){
												$licznik++;
												if($licznik > 1)
												//jeżeli nie jest to pierwsza linia danej grupy kursowej - dodawany jest na początku znacznik <tr>
												{
													$output .= '<tr>';
												}
												$output .= '<td>'.$kursy['dzien_tygodnia'][$i][$j].'</td><td>'.$kursy['godziny_dnia'][$i][$j].'</td></tr>';								
											}
										}
										//generowanie całej linii tabeli
										echo '<tr><td rowspan="'.$licznik.'">'.$grupa_kursowa_name[$j].'</td><td rowspan="'.$licznik.'">'.$grupa_kursowa_oplata[$j].'</td>'.$output;
									}
									
									
								?>

