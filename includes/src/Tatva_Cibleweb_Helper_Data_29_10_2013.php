<?php
/**
 * created : 02 oct. 2009
 * 
 * @category Tatva
 * @package Tatva_Cibleweb
 * @author emchaabelasri
 * @copyright Tatva - 2009 - http://www.Tatva.com
 * 
 * EXIG : 
 * REG  : 
 */

/**
 * Description of the class
 * @package Tatva_Cibleweb
 */
class Tatva_Cibleweb_Helper_Data extends Mage_Core_Helper_Abstract{

	public function firstLine(){
		return array(
			'id produit ',
			'dénomination concise',
			'dénomination subjective',
			'description concise',
			'description complète',
			'photo1',
			'photo2',
			'photo3',
			'photo4',
			'photo5',
			'URL fiche produit',
			'marque',
			'categorie',
			'id référence',
			'disponibilité du produit',
			'statut de disponibilité du produit',
			'prix TTC',
			'écotaxe',
			'garantie',
			'droit de rétractation',
			'quantité',
			'délai de livraison',
			'unité délai de livraison',
			'délai d expédition',
			'unité délai d expédition',
			'frais de port',
			'référence constructeur (optionnel)',
			'prix public généralement constaté (optionnel)',
			'ean13 (optionnel)',
			'mots-clefs (optionnel)',
			'date/heure de début promotion',
			'date/heure de fin promotion',
			'prix TTC avant promotion',
			'promotion (montant en euros)',
			'pourcentage de la démarque',
			'prix remisé',
			'genre',
			'matiere',
			'couleur',
			'taille',
			'pointure',
			'collection',
			'delai de reapprovisonnement',
			//add by nisha
			'Leguide_type_de_promotion'		);
	}
    public function xmlLine(){
		return array(
			'catagory ',
			'unique_id',
			'title',
			'description',
			'prix',
			'product_URL',
			'image_URL',
			'shipping_costs',
			'available_stock',
			'delivery_time',
			'warrenty',
			'model_refrence',
			'D3E',
			'brand',
			'ean',
			'oldprice',
			'currency',
			'state',
			'promotion_type',
			'color',
			'material',
			'size',
			'type'
			);
	}
	public function notSelectedAttributes(){
		return array (
						'description',
						'sku',
						'cost',
						'manufacturer',
						'meta_title',
						'meta_keyword',
						'meta_description',
						'image',
						'media_gallery',
						'tier_price',
						'gallery',
						'visibility',
						'custom_design',
						'custom_design_from',
						'custom_design_to',
						'custom_layout_update',
						'page_layout',
						'options_container',
						'enable_googlecheckout',
						'gift_message_available',
						'Tatva_special_from_hour',
						'Tatva_special_to_hour',
						'Tatva_special_qty',
						'Tatva_active_special_price',
						'azboutique_nb_ventes',
						'is_chequecadeau',
						'short_description',
						'price',
						'special_price',
						'special_from_date',
						'special_to_date',
						'small_image',
						'thumbnail',
						'news_from_date',
						'news_to_date',
						'status',
						'tax_class_id',
						'url_key',
						'price_view',
						'Tatva_productpush_specials_display',
						'Tatva_productpush_specials_position',
						'marque',
						'Tatva_popularity'
					);

	}


}